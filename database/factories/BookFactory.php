<?php
namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            "title" => fake()->sentence(3),
            "author" => fake()->name(),
            "genre" => fake()->randomElement(["fiction", "drama", "sci-fi", "biography"]),
            "description" => fake()->paragraph(),
            "numberOfPages" => fake()->numberBetween(100, 600),
            "language" => "sr",
            "publicationDate" => fake()->date(),
            "publisher" => fake()->company(),
            "remainingForLoan" => fake()->numberBetween(0, 15),
            "remainingForSell" => fake()->numberBetween(0, 10),
        ];
    }

    // Ugnežđene cene se ne mogu popuniti kroz obično definition() polje —
    // dodaju se posle kreiranja knjige preko embedsMany relacije.
    public function configure(): static
    {
        return $this->afterCreating(function (Book $book) {
            $book->prices()->create([
                "startDate" => now()->subMonth(),
                "endDate" => now()->addYear(),
                "price" => fake()->numberBetween(500, 3000),
            ]);
        });
    }
}
