<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Inertia\Inertia;
use App\Models\Uses;
use App\Http\Controllers\Controller;
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
        $book = Book::with("currentPrice")->findOrFail($id);

        if (Auth::check()) {
            Uses::create([
                "userId" => Auth::id(),
                "bookId" => $book->bookId,
                "type" => "view",
                "points" => 1,
            ]);
        }

        return Inertia::render("Books/Show", [
            "book" => $book,
            "relatedBooks" => Book::where("genre", $book->genre)
                ->where("bookId", "!=", $id)
                ->limit(4)
                ->get(),
        ]);
    }

    public function popular()
    {
        $books = Book::select("books.*")
            ->join("uses", "books.bookId", "=", "uses.bookId")
            ->where("uses.date", ">=", now()->subMonth())
            ->groupBy("books.bookId")
            ->orderByRaw("SUM(uses.points) DESC")
            ->limit(10)
            ->get();

        // Ako nema podataka, vrati random knjige
        if ($books->isEmpty()) {
            return Book::inRandomOrder()->limit(10)->get();
        }

        return $books;
    }

    public function recommendations()
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        // Knjige koje je korisnik već gledao
        $viewedBookIds = \App\Models\Uses::where("userId", $user->userId)
            ->pluck("bookId")
            ->unique();

        // Top 3 žanra korisnika
        $topGenres = \App\Models\Uses::where("userId", $user->userId)
            ->join("books", "uses.bookId", "=", "books.bookId")
            ->select("books.genre")
            ->groupBy("books.genre")
            ->orderByRaw("SUM(uses.points) DESC")
            ->limit(3)
            ->pluck("books.genre");

        if ($topGenres->isEmpty()) {
            return Book::whereNotIn("bookId", $viewedBookIds)
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        return Book::whereIn("genre", $topGenres)
            ->whereNotIn("bookId", $viewedBookIds)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }
}
