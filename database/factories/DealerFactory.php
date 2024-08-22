<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dealer>
 */
class DealerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "dealer_name" => fake()->name(),
            "email" => fake()->unique()->email(),
            "contact" => fake()->numberBetween(1000000000),
            "GST_no" => fake()->swiftBicNumber(),
        ];
    }
}
