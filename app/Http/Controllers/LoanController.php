<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::where("userId", Auth::id())
            ->with("book")
            ->orderBy("created_at", "desc")
            ->get();

        return Inertia::render("Loans/Index", [
            "loans" => $loans,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Provera pretplate
        if (!$user->hasActiveSubscription()) {
            return back()->withErrors([
                "loan" => "Potrebna je aktivna pretplata za pozajmicu.",
            ]);
        }

        // Provera broja dostupnih pozajmica
        if ($user->numberOfLoans <= 0) {
            return back()->withErrors([
                "loan" => "Nemate dostupnih pozajmica.",
            ]);
        }

        $fields = $request->validate([
            "bookId" => "required|exists:books,bookId",
            "deliveryType" => "required|in:physical,library",
        ]);

        $book = Book::findOrFail($fields["bookId"]);

        // Provera dostupnosti knjige
        if ($book->remainingForLoan <= 0) {
            return back()->withErrors([
                "loan" => "Knjiga nije dostupna za pozajmicu.",
            ]);
        }

        \App\Models\Uses::create([
            "userId" => $user->userId,
            "bookId" => $book->bookId,
            "type" => "loan",
            "points" => 5,
        ]);
        // Kreiranje pozajmice
        Loan::create([
            "bookId" => $book->bookId,
            "userId" => $user->userId,
            "loanDate" => now(),
            "endReturnDate" => now()->addDays(30),
            "status" => "manual_pickup_requested",
            "deliveryType" => $fields["deliveryType"],
            "remaining" => $book->remainingForLoan,
            "low_stock" => $book->remainingForLoan <= 10,
        ]);

        // Smanjenje dostupnih primeraka i broja pozajmica
        $book->decrement("remainingForLoan");
        $user->decrement("numberOfLoans");

        return redirect()
            ->route("loans.index")
            ->with("success", "Pozajmica uspešno kreirana!");
    }

    public function return(Request $request, $id)
    {
        $user = Auth::user();
        $loan = Loan::where("loanId", $id)
            ->where("userId", $user->userId)
            ->firstOrFail();

        $fields = $request->validate([
            "returnType" => "required|in:physical,library",
        ]);

        $loan->update([
            "returnDate" => now(),
            "status" => now()->gt($loan->endReturnDate)
                ? "return_late_started"
                : "return_on_time_started",
            "returnType" => $fields["returnType"],
        ]);

        // Vraćanje primerka i pozajmice korisniku
        $loan->book->increment("remainingForLoan");
        $user->increment("numberOfLoans");

        return redirect()
            ->route("loans.index")
            ->with("success", "Povratak knjige uspešno evidentiran!");
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            "status" =>
                "required|in:manual_pickup_requested,manual_picked_up,sending_by_post,post_arrived_for_pickup,postal_pickup,postal_pickup_cancelled,returned_unwanted_by_post,return_on_time_started,return_late_started,manual_return,postal_return,return_sent_by_post,return_arrived_by_post,returned_on_time,returned_late",
        ]);

        $loan = Loan::findOrFail($id);

        $data = ["status" => $request->status];

        // Ako je vraćena — postavi returnDate na danas
        if (in_array($request->status, ["returned_on_time", "returned_late"])) {
            $data["returnDate"] = now();

            // Vrati primerak i broj pozajmica korisniku
            $loan->book->increment("remainingForLoan");
            $loan->user->increment("numberOfLoans");
        }

        $loan->update($data);

        return back()->with("success", "Status uspešno promenjen.");
    }

    public function staffIndex()
    {
        return Inertia::render("Staff/Loans", [
            "loans" => Loan::with(["user", "book"])
                ->orderBy("created_at", "desc")
                ->paginate(15),
        ]);
    }
}
