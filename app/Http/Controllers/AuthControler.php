<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AuthControler extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            "avatar" => ["nullable", "image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
            "name" => "required",
            "surname" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6|confirmed",
        ]);

        if ($request->hasFile("avatar")) {
            $fields["avatar"] = base64_encode(
                file_get_contents($request->file("avatar")->getRealPath()),
            );
        }

        $fields["password"] = bcrypt($fields["password"]);

        $user = User::create($fields);

        if ($user->avatar) {
            Storage::disk("public")->put(
                "avatars/{$user->id}.png",
                base64_decode($user->avatar),
            );
        }

        Auth::login($user);

        return redirect()->route("home")->with("success", "Registracija uspešna!");
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (!Auth::attempt($fields)) {
            return back()->withErrors(["email" => "Pogrešan email ili šifra."]);
        }

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk("public")->put(
                "avatars/{$user->id}.png",
                base64_decode($user->avatar),
            );
        }

        $request->session()->regenerate();
        return redirect("/");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk("public")->delete("avatars/{$user->id}.png");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home");
    }
}
