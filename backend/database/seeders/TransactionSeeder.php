<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->info('É necessário pelo menos 2 usuários para gerar transações.');
            return;
        }

        for ($i = 0; $i < 10; $i++) {

            $sender = $users->random();
            $recipient = $users->where('id', '!=', $sender->id)->random();

            $amount = mt_rand(500, 5000) / 100;

            $date = Carbon::now()->subDays(rand(0, 180));

            Transaction::create([
                'user_id' => $sender->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'type' => 'sent',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            Transaction::create([
                'user_id' => $recipient->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'type' => 'received',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}