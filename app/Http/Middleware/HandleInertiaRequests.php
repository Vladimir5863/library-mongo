<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = "app";

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            "auth" => [
                "user" => fn() => $request->user()
                    ? [
                        "userId" => $request->user()->userId,
                        "name" => $request->user()->name,
                        "surname" => $request->user()->surname,
                        "avatar" => Storage::disk("public")->exists(
                            "avatars/{$request->user()->userId}.png",
                        )
                            ? "/storage/avatars/{$request->user()->userId}.png"
                            : null,
                        "userType" => $request->user()->userType,
                    ]
                    : null,
            ],
            "flash" => [
                "success" => fn() => $request->session()->get("success"),
            ],
        ]);
    }
}
