<?php
namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            "userId" => User::factory()->create()->id,
            "bookId" => Book::factory()->create()->id,
            "loanDate" => now()->subDays(fake()->numberBetween(1, 20)),
            "endReturnDate" => now()->addDays(30),
            "status" => "manual_pickup_requested",
            "deliveryType" => fake()->randomElement(["physical", "library"]),
            "remaining" => fake()->numberBetween(0, 10),
            "low_stock" => false,
        ];
    }
}
