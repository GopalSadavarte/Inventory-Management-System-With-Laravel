<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "customer_name" => fake()->name(),
            "customer_email" => fake()->unique()->email(),
            "contact" => fake()->numberBetween(1000000000),
            "customer_address" => fake()->address(),
            "pending_amt" => fake()->numberBetween(100, 5000),
        ];
    }
}
