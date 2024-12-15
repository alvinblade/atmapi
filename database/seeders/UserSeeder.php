<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 10; $i++) {
            User::query()->create([
                'username' => "mrdvalvin$i",
                'email' => "mrdvalvin$i@gmail.com",
                'password' => Hash::make("123456"),
                'name' => "Elvin$i",
                'surname' => "Muradov$i",
                'is_admin' => array_rand([true, false]),
                'is_featured' => array_rand([true, false])
            ]);
        }
    }
}
