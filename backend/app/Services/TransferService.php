<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService {
    public function transferUser(User $sender, User $recipient, float $amount): array
    {
        if ($sender->id === $recipient->id) {
            throw new Exception("Não pode transferir para si mesmo.", 422);
        }

        if ($amount <= 0) {
            throw new Exception("A quantia deve ser maior que zero.", 422);
        }

        if ($sender->wallet->balance < $amount) {
            throw new Exception("Saldo insuficiente para realizar a transferência.", 422);
        }

        try{
            $dbTransaction = DB::transaction(function () use ($sender, $recipient, $amount) {
                $sender->wallet->decrement('balance', $amount);

                $recipient->wallet->increment('balance', $amount);

                // Sent transaction
                Transaction::create([
                    'user_id' => $sender->id,
                    'sender_id' => $sender->id,
                    'recipient_id' => $recipient->id,
                    'amount' => $amount,
                    'type' => 'sent',
                ]);

                // Received transaction
                Transaction::create([
                    'user_id' => $recipient->id,
                    'sender_id' => $sender->id,
                    'recipient_id' => $recipient->id,
                    'amount' => $amount,
                    'type' => 'received',
                ]);
                
                return [
                    'success' => true,
                    'message' => "Transferência de R$ {$amount} realizada com sucesso.",
                    'sender_balance' => $sender->wallet->fresh()->balance,
                    'recipient_balance' => $recipient->wallet->fresh()->balance,
                ];
            });

            return $dbTransaction;

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Erro ao processar a transferência: " . $e->getMessage()
            ];
        }
    }
}