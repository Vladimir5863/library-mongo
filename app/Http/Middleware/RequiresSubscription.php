<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequiresSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route("login");
        }

        if (!Auth::user()->hasActiveSubscription()) {
            return redirect()
                ->route("subscription.index")
                ->withErrors([
                    "subscription" => "Potrebna je aktivna pretplata.",
                ]);
        }

        return $next($request);
    }
}
