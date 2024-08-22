<?php

namespace Database\Seeders;

use App\Models\CustomerProduct;
use Illuminate\Database\Seeder;

class CustomerProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 20; $i++) {
            CustomerProduct::create([
                "p_id" => fake()->numberBetween(1, 10),
                "c_id" => fake()->numberBetween(1, 10),
            ]);
        }
    }
}
