<?php
namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Book;
use App\Models\Uses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasActiveSubscription()) {
            return back()->withErrors([
                "sell" => "Potrebna je aktivna pretplata za kupovinu.",
            ]);
        }

        $fields = $request->validate([
            "bookId" => "required|exists:books,_id",
            "deliveryType" => "required|in:physical,library",
        ]);

        $book = Book::findOrFail($fields["bookId"]);

        if ($book->remainingForSell <= 0) {
            return back()->withErrors([
                "sell" => "Knjiga nije dostupna za kupovinu.",
            ]);
        }

        $currentPrice = $book->currentPrice();

        if (!$currentPrice) {
            return back()->withErrors([
                "sell" => "Knjiga trenutno nema definisanu cenu.",
            ]);
        }

        $price = $currentPrice->price;
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription && $activeSubscription->price == 2999) {
            $price = round($price * 0.9);
        }

        Sell::create([
            "bookId" => $book->id,
            "userId" => $user->id,
            "sellDate" => now(),
            "deliveryType" => $fields["deliveryType"],
            "price" => $price,
            "remaining" => $book->remainingForSell - 1,
            "low_stock" => $book->remainingForSell <= 2,
        ]);

        $book->decrement("remainingForSell");

        Uses::create([
            "userId" => $user->id,
            "bookId" => $book->id,
            "type" => "sell",
            "points" => 10,
        ]);

        return redirect()
            ->route("home")
            ->with("success", "Kupovina uspešna! Plaćeno: {$price} RSD");
    }
}
