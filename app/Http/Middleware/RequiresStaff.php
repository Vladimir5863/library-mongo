<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequiresStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route("login");
        }

        if (
            !in_array(Auth::user()->userType, ["admin", "librarian", "postman"])
        ) {
            return redirect()->route("home");
        }

        return $next($request);
    }
}
