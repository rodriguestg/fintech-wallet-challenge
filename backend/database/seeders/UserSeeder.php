<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Usuário $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
            ]);
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 1000.00,
            ]);
        }
    }
}
