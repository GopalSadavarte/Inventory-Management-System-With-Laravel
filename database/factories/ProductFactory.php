<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected static $pId = "MR0";
    public function definition(): array
    {
        $product = substr(ProductFactory::$pId, 2);
        $id = "MR" . (intval($product) + 1);

        ProductFactory::$pId = $id;
        return [
            "product_id" => $id,
            'group_no' => fake()->numberBetween(1, 10),
            'sub_group_no' => fake()->numberBetween(1, 10),
            "product_name" => fake()->unique()->name(),
            "quantity" => 1,
            "rate" => fake()->numberBetween(50, 1000),
            "MRP" => fake()->numberBetween(100, 1500),
            "discount" => 10,
            'GST' => 12,
            'GSTOn' => 'sale rate',
        ];
    }
}
