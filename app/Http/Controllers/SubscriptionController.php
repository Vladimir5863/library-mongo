<?php
namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render("Subscription/Index", [
            "subscription" => $user->activeSubscription,
            "hasActive" => $user->hasActiveSubscription(),
            "history" => Subscription::where("userId", $user->userId)
                ->orderBy("created_at", "desc")
                ->get(),
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
        $endDate =
            $fields["plan"] === "yearly" ? now()->addYear() : now()->addMonth();

        Subscription::create([
            "userId" => $user->userId,
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

    public function create()
    {
        return Inertia::render("Subscription/Create", [
            "accountNumber" => Auth::user()->accountNumber,
        ]);
    }

    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription()->first();

        if (!$subscription) {
            return back()->withErrors([
                "subscription" => "Nemate aktivnu pretplatu.",
            ]);
        }

        $subscription->update([
            "endDate" => now()->subDay(),
            "active" => false,
        ]);

        return redirect()
            ->route("subscription.index")
            ->with("success", "Pretplata otkazana.");
    }
}
