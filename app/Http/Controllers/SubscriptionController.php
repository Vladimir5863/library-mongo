<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render("Subscription/Index", [
            "subscription" => $user->activeSubscription(),
            "hasActive" => $user->hasActiveSubscription(),
            "history" => $user->subscriptions()
                ->get()
                ->sortByDesc("created_at")
                ->values(),
        ]);
    }

    public function create()
    {
        return Inertia::render("Subscription/Create", [
            "accountNumber" => Auth::user()->accountNumber,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasActiveSubscription()) {
            return back()->withErrors([
                "subscription" => "Već imate aktivnu pretplatu.",
            ]);
        }

        $fields = $request->validate([
            "plan" => "required|in:monthly,yearly",
            "accountNumber" => "required|string|min:10|max:18",
            "autoRenew" => "boolean",
        ]);

        $price = $fields["plan"] === "yearly" ? 2999 : 299;
        $endDate = $fields["plan"] === "yearly" ? now()->addYear() : now()->addMonth();

        $user->subscriptions()->create([
            "startDate" => now(),
            "endDate" => $endDate,
            "price" => $price,
            "active" => true,
            "accountNumber" => $fields["accountNumber"],
            "autoRenew" => $fields["autoRenew"] ?? false,
        ]);

        return redirect()
            ->route("subscription.index")
            ->with("success", "Pretplata uspešno aktivirana!");
    }

    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return back()->withErrors([
                "subscription" => "Nemate aktivnu pretplatu.",
            ]);
        }

        $subscription->endDate = now()->subDay();
        $subscription->active = false;
        $user->subscriptions()->save($subscription);

        return redirect()
            ->route("subscription.index")
            ->with("success", "Pretplata otkazana.");
    }
}
