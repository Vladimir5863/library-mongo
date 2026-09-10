<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Price;
use App\Models\Subscription;
use App\Models\Loan;
use App\Models\Sell;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $lowStockCount = Book::whereHas(
            "loans",
            fn($q) => $q->where("low_stock", true),
        )
            ->orWhereHas("sells", fn($q) => $q->where("low_stock", true))
            ->count();

        return Inertia::render("Admin/Index", [
            "stats" => [
                "users" => User::count(),
                "books" => Book::count(),
                "activeSubscriptions" => Subscription::where(
                    "active",
                    true,
                )->count(),
                "activeLoans" => Loan::whereNotIn("status", [
                    "returned_on_time",
                    "returned_late",
                ])->count(),
                "lowStockCount" => $lowStockCount,
            ],
        ]);
    }

    public function users()
    {
        return Inertia::render("Admin/Users", [
            "users" => User::withTrashed()
                ->orderBy("created_at", "desc")
                ->paginate(15),
        ]);
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with("success", "Korisnik banovan.");
    }

    public function unbanUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return back()->with("success", "Korisnik odbanovan.");
    }

    public function changeRole(Request $request, $id)
    {
        $request->validate([
            "userType" => "required|in:admin,librarian,user,postman",
        ]);

        User::findOrFail($id)->update(["userType" => $request->userType]);

        return back()->with("success", "Uloga promenjena.");
    }

    public function subscriptions()
    {
        return Inertia::render("Admin/Subscriptions", [
            "subscriptions" => Subscription::with("user")
                ->orderBy("created_at", "desc")
                ->paginate(15),
        ]);
    }

    public function books()
    {
        return Inertia::render("Admin/Books", [
            "books" => Book::with("currentPrice")
                ->orderBy("title")
                ->paginate(15),
        ]);
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            "price" => "required|integer|min:0",
            "startDate" => "required|date",
            "endDate" => "required|date|after:startDate",
        ]);

        $book = Book::findOrFail($id);

        //Deaktiviraj staru cenu
        Price::where("bookId", $book->bookId)
            ->whereDate("endDate", ">=", now())
            ->update(["endDate" => now()->subDay()]);

        // Dodaj novu cenu
        Price::create([
            "bookId" => $book->bookId,
            "startDate" => $request->startDate,
            "endDate" => $request->endDate,
            "price" => $request->price,
        ]);

        return back()->with("success", "Cena ažurirana.");
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            "remainingForLoan" => "required|integer|min:0",
            "remainingForSell" => "required|integer|min:0",
        ]);

        $book = Book::findOrFail($id);

        $book->update([
            "remainingForLoan" => $request->remainingForLoan,
            "remainingForSell" => $request->remainingForSell,
        ]);

        // Reset low_stock flag for all linked records, jer je zaliha osvežena.
        Loan::where("bookId", $book->bookId)
            ->where("low_stock", true)
            ->update(["low_stock" => false]);

        Sell::where("bookId", $book->bookId)
            ->where("low_stock", true)
            ->update(["low_stock" => false]);

        return back()->with("success", "Stanje ažurirano.");
    }

    public function loans()
    {
        return Inertia::render("Admin/Loans", [
            "loans" => Loan::with(["user", "book"])
                ->orderBy("created_at", "desc")
                ->paginate(15),
        ]);
    }

    public function getLowStockBooks()
    {
        // Pokupi ID-jeve knjiga koje imaju low_stock stanje u jednoj ili obe tabele
        $loanBookIds = Book::whereHas(
            "loans",
            fn($q) => $q->where("low_stock", true),
        )->pluck("bookId");

        $sellBookIds = Book::whereHas(
            "sells",
            fn($q) => $q->where("low_stock", true),
        )->pluck("bookId");

        $allBooks = $loanBookIds->concat($sellBookIds)->unique();

        // Učitaj knjige jedinstveno po bookId
        $books = Book::whereIn("bookId", $allBooks)->get();

        $alerts = $books->map(function ($book) use (
            $loanBookIds,
            $sellBookIds,
        ) {
            $loanLow = $loanBookIds->contains($book->bookId);
            $sellLow = $sellBookIds->contains($book->bookId);

            $type =
                $loanLow && $sellLow
                    ? "loan/sell"
                    : ($sellLow
                        ? "sell"
                        : "loan");

            return [
                "book" => $book,
                "type" => $type,
            ];
        });

        return Inertia::render("Admin/BookAlert", [
            "alerts" => $alerts,
        ]);
    }
}
