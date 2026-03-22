<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_list_all_transactions()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Wallet::create(['user_id' => $sender->id, 'balance' => 1000]);
        Wallet::create(['user_id' => $recipient->id, 'balance' => 0]);

        Transaction::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'amount' => 100,
            'type' => 'sent',
        ]);

        Transaction::create([
            'sender_id' => $recipient->id,
            'recipient_id' => $sender->id,
            'amount' => 100,
            'type' => 'received',
        ]);

        $response = $this->actingAs($sender)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [ '*' => ['id', 'sender_id', 'recipient_id', 'amount', 'type', 'created_at']], 'meta', 'links']);
    }
}
