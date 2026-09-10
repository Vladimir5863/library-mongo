<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Loan;
use App\Models\Sell;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render("Profile/Index", [
            "user" => $user,
            "stats" => [
                "totalLoans" => Loan::where("userId", $user->userId)->count(),
                "totalSells" => Sell::where("userId", $user->userId)->count(),
                "hasSubscription" => $user->hasActiveSubscription(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $fields = $request->validate([
            "name" => "required|string|max:255",
            "surname" => "required|string|max:255",
            "email" => [
                "required",
                "email",
                Rule::unique("users", "email")->ignore($user->userId, "userId"),
            ],
        ]);

        $user->update($fields);

        return back()->with("success", "Profil ažuriran.");
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            "current_password" => "required",
            "password" => "required|min:6|confirmed",
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                "current_password" => "Trenutna lozinka nije ispravna.",
            ]);
        }

        $user->update(["password" => bcrypt($request->password)]);

        return back()->with("success", "Lozinka promenjena.");
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            "avatar" => "required|image|mimes:jpeg,png,jpg|max:2048",
        ]);

        $user = Auth::user();

        // Obriši stari avatar iz foldera
        Storage::disk("public")->delete("avatars/{$user->userId}.png");

        // Sačuvaj novi u bazu i folder
        $base64 = base64_encode(
            file_get_contents($request->file("avatar")->getRealPath()),
        );

        $user->update(["avatar" => $base64]);

        Storage::disk("public")->put(
            "avatars/{$user->userId}.png",
            base64_decode($base64),
        );

        return back()->with("success", "Avatar ažuriran.");
    }

    public function updateAccount(Request $request)
    {
        $request->validate([
            "accountNumber" => "required|string|min:10|max:18",
        ]);

        Auth::user()->update(["accountNumber" => $request->accountNumber]);

        return back()->with("success", "Broj računa ažuriran.");
    }
}
