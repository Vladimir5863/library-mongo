<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Uses;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index()
    {
        return Inertia::render("Books/Index", [
            "books" => Book::paginate(20),
        ]);
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        if (Auth::check()) {
            Uses::create([
                "userId" => Auth::id(),
                "bookId" => $book->id,
                "type" => "view",
                "points" => 1,
            ]);
        }

        return Inertia::render("Books/Show", [
            "book" => $book,
            "currentPrice" => $book->currentPrice(),
            "relatedBooks" => Book::where("genre", $book->genre)
                ->where("_id", "!=", $id)
                ->limit(4)
                ->get(),
        ]);
    }

    public function popular()
    {
        // Mongo nema JOIN — dvostepeni upit: prvo agregacija poena po knjizi, zatim učitavanje knjiga.
        $topBookIds = Uses::where("date", ">=", now()->subMonth())
            ->get()
            ->groupBy("bookId")
            ->map(fn($group) => $group->sum("points"))
            ->sortDesc()
            ->take(10)
            ->keys();

        $books = Book::whereIn("_id", $topBookIds)->get();

        return $books->isEmpty()
            ? Book::inRandomOrder()->limit(10)->get()
            : $books;
    }

    public function recommendations()
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        $viewedBookIds = Uses::where("userId", $user->id)
            ->pluck("bookId")
            ->unique();

        $topGenres = Uses::where("userId", $user->id)
            ->get()
            ->map(fn($use) => Book::find($use->bookId)?->genre)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(3)
            ->keys();

        $query = Book::whereNotIn("_id", $viewedBookIds);

        return $topGenres->isEmpty()
            ? $query->inRandomOrder()->limit(10)->get()
            : $query
                ->whereIn("genre", $topGenres)
                ->inRandomOrder()
                ->limit(10)
                ->get();
    }
}
