<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\bill>
 */
class billFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private static $num = 0;
    public function definition(): array
    {
        billFactory::$num++;
        return [
            "dayWiseBillNumber" => billFactory::$num,
            "bill_amount" => fake()->numberBetween(50, 1000),
            "customer_id" => fake()->numberBetween(1, 100),
        ];
    }
}
