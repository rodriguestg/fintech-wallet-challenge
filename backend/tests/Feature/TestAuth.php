<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class TestAuth extends TestCase
{
    use RefreshDatabase;

     public function test_user_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Gabriel Test',
            'email' => 'testGabriel@example.com',
            'password' => '10203040',
            'password_confirmation' => '10203040',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['token']);

        $user = User::where('email', 'testGabriel@example.com')->first();
        $this->assertEquals(1000.00, $user->wallet->balance);
    }

    public function test_user_login(){
        User::factory()->create([
            'email' => 'testGabriel@example.com',
            'password' => Hash::make('10203040'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testGabriel@example.com',
            'password' => '10203040',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['token']);
    }

    public function test_user_login_invalid_credentials(){
        User::factory()->create([
            'email' => 'testGabriel@example.com',
            'password' => Hash::make('10203040'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testGabriel@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

}
