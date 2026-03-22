<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;

class TransferTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    
    public function test_user_can_transfer_money_to_another_user()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Wallet::create(['user_id' => $sender->id, 'balance' => 1000]);
        Wallet::create(['user_id' => $recipient->id, 'balance' => 0]);

        $response = $this->actingAs($sender)->postJson('/api/transfers', [
            'recipient_email' => $recipient->email,
            'amount' => 100,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(900, $sender->wallet->fresh()->balance);
        $this->assertEquals(100, $recipient->wallet->fresh()->balance);
    }
}
