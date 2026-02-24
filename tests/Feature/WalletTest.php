<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a wallet with valid data
     */
    public function test_can_create_wallet_with_valid_data(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/wallets', [
            'user_id' => $user->id,
            'name' => 'Personal Account',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Wallet created successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Personal Account',
                    'balance' => 0,
                ],
            ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'Personal Account',
        ]);
    }

    /**
     * Test creating wallet fails with missing required fields
     */
    public function test_cannot_create_wallet_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/wallets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'name']);
    }

    /**
     * Test creating wallet fails with non-existent user
     */
    public function test_cannot_create_wallet_with_non_existent_user(): void
    {
        $response = $this->postJson('/api/v1/wallets', [
            'user_id' => 'invalid-user-id',
            'name' => 'Personal Account',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id']);
    }

    /**
     * Test viewing wallet details with no transactions
     */
    public function test_can_view_wallet_with_no_transactions(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}");

        $response->assertStatus(200)
            ->assertJson([
                'wallet' => [
                    'id' => $wallet->id,
                    'user_id' => $user->id,
                    'name' => 'Personal',
                    'balance' => 0,
                ],
                'transactions' => [],
            ]);
    }

    /**
     * Test viewing wallet with transactions
     */
    public function test_can_view_wallet_with_transactions(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $wallet->transactions()->create([
            'type' => 'income',
            'amount' => 1000,
            'description' => 'Salary',
            'date' => '2026-02-24',
        ]);

        $wallet->transactions()->create([
            'type' => 'expense',
            'amount' => 200,
            'description' => 'Groceries',
            'date' => '2026-02-24',
        ]);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'transactions')
            ->assertJson([
                'wallet' => [
                    'balance' => 800,
                ],
            ]);
    }

    /**
     * Test updating wallet name
     */
    public function test_can_update_wallet_name(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->putJson("/api/v1/wallets/{$wallet->id}", [
            'name' => 'Updated Personal Account',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Wallet updated successfully.',
                'data' => [
                    'name' => 'Updated Personal Account',
                ],
            ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'name' => 'Updated Personal Account',
        ]);
    }

    /**
     * Test updating wallet fails with missing name
     */
    public function test_cannot_update_wallet_without_name(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->putJson("/api/v1/wallets/{$wallet->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test deleting a wallet
     */
    public function test_can_delete_wallet(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->deleteJson("/api/v1/wallets/{$wallet->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Wallet deleted successfully.',
            ]);

        $this->assertDatabaseMissing('wallets', [
            'id' => $wallet->id,
        ]);
    }

    /**
     * Test deleting wallet also deletes transactions
     */
    public function test_deleting_wallet_cascades_to_transactions(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $transaction = $wallet->transactions()->create([
            'type' => 'income',
            'amount' => 1000,
            'description' => 'Salary',
            'date' => '2026-02-24',
        ]);

        $this->deleteJson("/api/v1/wallets/{$wallet->id}");

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    /**
     * Test viewing non-existent wallet returns 404
     */
    public function test_cannot_view_non_existent_wallet(): void
    {
        $response = $this->getJson('/api/v1/wallets/invalid-id');

        $response->assertStatus(404);
    }
}
