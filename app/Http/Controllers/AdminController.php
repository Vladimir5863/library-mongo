<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Sell;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $lowStockCount = Book::whereHas("loans", fn($q) => $q->where("low_stock", true))
            ->orWhereHas("sells", fn($q) => $q->where("low_stock", true))
            ->count();

        return Inertia::render("Admin/Index", [
            "stats" => [
                "users" => User::count(),
                "books" => Book::count(),
                // Dot-notacija upit nad ugnežđenim nizom — pogađa korisnike koji imaju
                // BAR JEDNU pretplatu sa active=true (poslovno pravilo dozvoljava najviše jednu).
                "activeSubscriptions" => User::where("subscriptions.active", true)->count(),
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
            "users" => User::withTrashed()->orderBy("created_at", "desc")->paginate(15),
        ]);
    }

    public function banUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with("success", "Korisnik banovan.");
    }

    public function unbanUser($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
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
        // Nema više jedne kolekcije za paginaciju — pretplate su ugnežđene po korisniku.
        // Spljoštavamo ih ovde i ručno paginiramo (kompromis koji embedding uvodi).
        $page = request("page", 1);
        $perPage = 15;

        $allSubscriptions = User::all()->flatMap(function ($user) {
            return $user->subscriptions()->get()->map(function ($sub) use ($user) {
                $sub->setRelation("user", $user);
                return $sub;
            });
        })->sortByDesc("created_at")->values();

        $items = $allSubscriptions->slice(($page - 1) * $perPage, $perPage)->values();

        return Inertia::render("Admin/Subscriptions", [
            "subscriptions" => [
                "data" => $items,
                "total" => $allSubscriptions->count(),
                "current_page" => (int) $page,
                "per_page" => $perPage,
            ],
        ]);
    }

    public function books()
    {
        $books = Book::orderBy("title")->paginate(15);
        $books->getCollection()->transform(function ($book) {
            $book->currentPriceValue = $book->currentPrice();
            return $book;
        });

        return Inertia::render("Admin/Books", ["books" => $books]);
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            "price" => "required|integer|min:0",
            "startDate" => "required|date",
            "endDate" => "required|date|after:startDate",
        ]);

        $book = Book::findOrFail($id);

        $oldPrice = $book->prices()->get()->first(
            fn($p) => $p->endDate >= now(),
        );

        if ($oldPrice) {
            $oldPrice->endDate = now()->subDay();
            $book->prices()->save($oldPrice);
        }

        $book->prices()->create([
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

        Loan::where("bookId", $book->id)->where("low_stock", true)->update(["low_stock" => false]);
        Sell::where("bookId", $book->id)->where("low_stock", true)->update(["low_stock" => false]);

        return back()->with("success", "Stanje ažurirano.");
    }

    public function loans()
    {
        return Inertia::render("Admin/Loans", [
            "loans" => Loan::with(["user", "book"])->orderBy("created_at", "desc")->paginate(15),
        ]);
    }

    public function getLowStockBooks()
    {
        $loanBookIds = Book::whereHas("loans", fn($q) => $q->where("low_stock", true))->pluck("_id");
        $sellBookIds = Book::whereHas("sells", fn($q) => $q->where("low_stock", true))->pluck("_id");

        $allBooks = $loanBookIds->concat($sellBookIds)->unique();
        $books = Book::whereIn("_id", $allBooks)->get();

        $alerts = $books->map(function ($book) use ($loanBookIds, $sellBookIds) {
            $loanLow = $loanBookIds->contains($book->id);
            $sellLow = $sellBookIds->contains($book->id);

            return [
                "book" => $book,
                "type" => $loanLow && $sellLow ? "loan/sell" : ($sellLow ? "sell" : "loan"),
            ];
        });

        return Inertia::render("Admin/BookAlert", ["alerts" => $alerts]);
    }
}
