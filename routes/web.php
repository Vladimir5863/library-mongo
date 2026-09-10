<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthControler;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use Inertia\Inertia;
use App\Models\Book;
use App\Models\News;

// Home
Route::get("/", function () {
    $controller = app(BookController::class);
    return Inertia::render("Home", [
        "books" => $controller->popular(),
        "latestNews" => News::whereDate("endDate", ">=", now())
            ->orderBy("startDate", "desc")
            ->limit(10)
            ->get(),
        "recommendations" => Auth::check()
            ? $controller->recommendations()
            : [],
    ]);
})->name("home");

// Auth
Route::group([], function () {
    Route::get("/register", fn() => Inertia::render("Auth/Register"))->name(
        "register",
    );
    Route::post("/register", [AuthControler::class, "register"])->name(
        "register.post",
    );
    Route::get("/login", fn() => Inertia::render("Auth/Login"))->name("login");
    Route::post("/login", [AuthControler::class, "login"])->name("login.post");
    Route::post("/logout", [AuthControler::class, "logout"])->name("logout");
});

// Books
Route::prefix("books")->group(function () {
    Route::get("/", [BookController::class, "index"])->name("books");
    Route::get("/{id}", [BookController::class, "show"])->name("books.show");
});

// Ostale stranice

Route::middleware(["auth", "subscribed"])->group(function () {
    Route::get("/loans", [LoanController::class, "index"])->name("loans.index");
    Route::post("/loans", [LoanController::class, "store"])->name(
        "loans.store",
    );
    Route::patch("/loans/{id}/return", [LoanController::class, "return"])->name(
        "loans.return",
    );
    Route::post("/sell", [SellController::class, "store"])->name("sell.store");
});

Route::middleware("auth")->group(function () {
    Route::get("/subscription", [SubscriptionController::class, "index"])->name(
        "subscription.index",
    );
    Route::get("/subscription/create", [
        SubscriptionController::class,
        "create",
    ])->name("subscription.create");
    Route::post("/subscription", [
        SubscriptionController::class,
        "store",
    ])->name("subscription.store");
    Route::delete("/subscription", [
        SubscriptionController::class,
        "cancel",
    ])->name("subscription.cancel");
});

Route::get("/news", [NewsController::class, "index"])->name("news.index");

Route::middleware(["auth"])->group(function () {
    Route::get("/news/create", [NewsController::class, "create"])->name(
        "news.create",
    );
    Route::post("/news", [NewsController::class, "store"])->name("news.store");
    Route::delete("/news/{id}", [NewsController::class, "destroy"])->name(
        "news.destroy",
    );
    Route::get("/news/{id}", [NewsController::class, "show"])->name(
        "news.show",
    );
});

use App\Http\Controllers\AdminController;

Route::middleware(["auth", "admin"])
    ->prefix("admin")
    ->group(function () {
        Route::get("/", [AdminController::class, "index"])->name("admin.index");
        Route::get("/users", [AdminController::class, "users"])->name(
            "admin.users",
        );
        Route::delete("/users/{id}/ban", [
            AdminController::class,
            "banUser",
        ])->name("admin.users.ban");
        Route::post("/users/{id}/unban", [
            AdminController::class,
            "unbanUser",
        ])->name("admin.users.unban");
        Route::patch("/users/{id}/role", [
            AdminController::class,
            "changeRole",
        ])->name("admin.users.role");
        Route::get("/subscriptions", [
            AdminController::class,
            "subscriptions",
        ])->name("admin.subscriptions");
        Route::get("/books", [AdminController::class, "books"])->name(
            "admin.books",
        );
        Route::patch("/books/{id}/price", [
            AdminController::class,
            "updatePrice",
        ])->name("admin.books.price");
        Route::patch("/books/{id}/stock", [
            AdminController::class,
            "updateStock",
        ])->name("admin.books.stock");
        Route::get("/loans", [AdminController::class, "loans"])->name(
            "admin.loans",
        );
        Route::get("/bookalert", [
            AdminController::class,
            "getLowStockBooks",
        ])->name("admin.bookalert");
    });

Route::middleware(["auth", "staff"])->group(function () {
    Route::get("/staff/loans", [LoanController::class, "staffIndex"])->name(
        "staff.loans",
    );
    Route::patch("/loans/{id}/status", [
        LoanController::class,
        "updateStatus",
    ])->name("loans.status");
});

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "index"])->name(
        "profile.index",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::patch("/profile/password", [
        ProfileController::class,
        "updatePassword",
    ])->name("profile.password");
    Route::post("/profile/avatar", [
        ProfileController::class,
        "updateAvatar",
    ])->name("profile.avatar");
});
