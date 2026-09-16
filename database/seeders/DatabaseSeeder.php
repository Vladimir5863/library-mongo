<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Price;
use App\Models\Subscription;
use App\Models\Loan;
use App\Models\Sell;
use App\Models\Uses;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

/*
public function run(): void
    {
        // Users
        $admin = User::create([
            "name" => "Admin",
            "surname" => "Adminovic",
            "email" => "admin@library.com",
            "password" => Hash::make("password"),
            "userType" => "admin",
            "numberOfLoans" => 5,
        ]);

        $librarian = User::create([
            "name" => "Marija",
            "surname" => "Petrovic",
            "email" => "librarian@library.com",
            "password" => Hash::make("password"),
            "userType" => "librarian",
            "numberOfLoans" => 5,
        ]);

        $postman = User::create([
            "name" => "Nikola",
            "surname" => "Nikolic",
            "email" => "postman@library.com",
            "password" => Hash::make("password"),
            "userType" => "postman",
            "numberOfLoans" => 5,
        ]);

        $users = User::factory(1000)->create();
        $allUsers = $users->concat([$admin, $librarian, $postman]);

        // Books
        $books = json_decode(
            file_get_contents(database_path("seeders/data/books.json")),
            true,
        );

        $createdBooks = collect();
        foreach ($books as $book) {
            $createdBooks->push(Book::create($book));
        }

        // Prices
        foreach ($createdBooks as $book) {
            Price::create([
                "bookId" => $book->bookId,
                "startDate" => "2026-01-01",
                "endDate" => "2026-12-31",
                "price" => rand(500, 3000),
            ]);
        }

        // News
        $newsData = json_decode(
            file_get_contents(database_path("seeders/data/news.json")),
            true,
        );

        foreach ($newsData as $item) {
            News::create($item);
        }

        // Subscriptions
        foreach ($users->take(15) as $user) {
            $plan = rand(0, 1) ? "monthly" : "yearly";
            $price = $plan === "yearly" ? 2999 : 299;
            $endDate =
                $plan === "yearly" ? now()->addYear() : now()->addMonth();

            Subscription::create([
                "userId" => $user->userId,
                "startDate" => now()->subDays(rand(1, 30)),
                "endDate" => $endDate,
                "price" => $price,
                "active" => true,
                "accountNumber" => "160-" . rand(100000000, 999999999) . "-12",
                "autoRenew" => rand(0, 1),
            ]);
        }

        // Expired subscriptions
        foreach ($users->take(5) as $user) {
            Subscription::create([
                "userId" => $user->userId,
                "startDate" => now()->subMonths(3),
                "endDate" => now()->subMonths(2),
                "price" => 299,
                "active" => false,
                "accountNumber" => "160-" . rand(100000000, 999999999) . "-12",
                "autoRenew" => false,
            ]);
        }

        // Loans
        $statuses = [
            "manual_pickup_requested",
            "manual_picked_up",
            "sending_by_post",
            "postal_pickup",
            "return_on_time_started",
            "returned_on_time",
            "returned_late",
        ];

        foreach ($users->take(15) as $user) {
            $numLoans = rand(1, 3);
            for ($i = 0; $i < $numLoans; $i++) {
                $book = $createdBooks->random();
                $isLowStock = $book->remainingForLoan <= 2;
                $loanDate = now()->subDays(rand(1, 60));
                $status = $statuses[array_rand($statuses)];
                $isReturned = in_array($status, [
                    "returned_on_time",
                    "returned_late",
                ]);

                Loan::create([
                    "bookId" => $book->bookId,
                    "userId" => $user->userId,
                    "loanDate" => $loanDate,
                    "endReturnDate" => $loanDate->copy()->addDays(30),
                    "returnDate" => $isReturned ? now() : null,
                    "status" => $status,
                    "deliveryType" => rand(0, 1) ? "library" : "physical",
                    "returnType" => $isReturned
                        ? (rand(0, 1)
                            ? "library"
                            : "physical")
                        : null,
                    "low_stock" => $isLowStock,
                ]);

                Uses::create([
                    "userId" => $user->userId,
                    "bookId" => $book->bookId,
                    "type" => "loan",
                    "points" => 5,
                    "date" => $loanDate,
                ]);
            }
        }

        // Sells
        foreach ($users->take(10) as $user) {
            $numSells = rand(1, 2);
            for ($i = 0; $i < $numSells; $i++) {
                $book = $createdBooks->random();
                $isLowStock = $book->remainingForSell <= 2;
                Sell::create([
                    "bookId" => $book->bookId,
                    "userId" => $user->userId,
                    "sellDate" => now()->subDays(rand(1, 60)),
                    "deliveryType" => rand(0, 1) ? "library" : "physical",
                    "low_stock" => $isLowStock,
                ]);

                Uses::create([
                    "userId" => $user->userId,
                    "bookId" => $book->bookId,
                    "type" => "sell",
                    "points" => 10,
                    "date" => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // Uses - view
        foreach ($users as $user) {
            $numViews = rand(5, 15);
            for ($i = 0; $i < $numViews; $i++) {
                Uses::create([
                    "userId" => $user->userId,
                    "bookId" => $createdBooks->random()->bookId,
                    "type" => "view",
                    "points" => 1,
                    "date" => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info("Seeder završen!");
    }
        */
    public function run() :void{
            User::factory()->count(20)->create();
            Book::factory()->count(50)->create();
    }
}
