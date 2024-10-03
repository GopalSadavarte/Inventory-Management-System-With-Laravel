<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Gopal',
            'email' => 'gopalsadavarte555@gmail.com',
            'password' => bcrypt('grs2004311'),
        ]);
    }
}
