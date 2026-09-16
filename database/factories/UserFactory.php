<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            "name" => fake()->firstName(),
            "surname" => fake()->lastName(),
            "email" => fake()->unique()->safeEmail(),
            "password" => bcrypt("password"),
            "userType" => "user",
            "numberOfLoans" => 3,
            "accountNumber" => fake()->numerify("##########"),
        ];
    }
}
