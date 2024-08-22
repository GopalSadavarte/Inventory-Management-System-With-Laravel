<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(CustomerProductSeeder::class);
        Customer::factory(10)->create();
        // group::factory(10)->create();
        // sub_group::factory(10)->create();
        // product::factory(10)->create();
        // bill::factory(10)->create();
        // dealer::factory(10)->create();
    }
}
