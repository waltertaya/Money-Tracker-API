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

    /**
     * Test creating income transaction
     */
    public function test_can_create_income_transaction(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 5000.50,
            'description' => 'Monthly salary',
            'date' => '2026-02-24',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Transaction created successfully.',
                'data' => [
                    'type' => 'income',
                    'amount' => '5000.50',
                    'description' => 'Monthly salary',
                ],
                'wallet_balance' => 5000.50,
            ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 5000.50,
        ]);
    }

    /**
     * Test creating expense transaction
     */
    public function test_can_create_expense_transaction(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'expense',
            'amount' => 250.75,
            'description' => 'Office supplies',
            'date' => '2026-02-24',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Transaction created successfully.',
                'data' => [
                    'type' => 'expense',
                    'amount' => '250.75',
                    'description' => 'Office supplies',
                ],
                'wallet_balance' => -250.75,
            ]);
    }

    /**
     * Test creating transaction without description
     */
    public function test_can_create_transaction_without_description(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 1000,
            'date' => '2026-02-24',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Transaction created successfully.',
                'data' => [
                    'type' => 'income',
                    'amount' => '1000.00',
                ],
            ]);
    }

    /**
     * Test creating transaction fails with missing required fields
     */
    public function test_cannot_create_transaction_without_required_fields(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'amount', 'date']);
    }

    /**
     * Test creating transaction fails with invalid type
     */
    public function test_cannot_create_transaction_with_invalid_type(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'invalid',
            'amount' => 1000,
            'date' => '2026-02-24',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test creating transaction fails with zero or negative amount
     */
    public function test_cannot_create_transaction_with_zero_or_negative_amount(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 0,
            'date' => '2026-02-24',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    /**
     * Test creating transaction fails with invalid date
     */
    public function test_cannot_create_transaction_with_invalid_date(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $response = $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 1000,
            'date' => 'invalid-date',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /**
     * Test viewing wallet transactions
     */
    public function test_can_view_wallet_transactions(): void
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
            'date' => '2026-02-20',
        ]);

        $wallet->transactions()->create([
            'type' => 'expense',
            'amount' => 200,
            'description' => 'Groceries',
            'date' => '2026-02-24',
        ]);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}/transactions");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'transactions')
            ->assertJson([
                'wallet' => [
                    'id' => $wallet->id,
                    'name' => 'Personal',
                    'balance' => 800,
                ],
            ]);
    }

    /**
     * Test transactions are ordered by date descending
     */
    public function test_transactions_are_ordered_by_date_descending(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $transaction1 = $wallet->transactions()->create([
            'type' => 'income',
            'amount' => 1000,
            'date' => '2026-02-20',
        ]);

        $transaction2 = $wallet->transactions()->create([
            'type' => 'income',
            'amount' => 500,
            'date' => '2026-02-24',
        ]);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}/transactions");

        $transactions = $response->json('transactions');

        // Most recent transaction should be first
        $this->assertEquals($transaction2->id, $transactions[0]['id']);
        $this->assertEquals($transaction1->id, $transactions[1]['id']);
    }

    /**
     * Test viewing specific transaction
     */
    public function test_can_view_specific_transaction(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        $transaction = $wallet->transactions()->create([
            'type' => 'income',
            'amount' => 5000.50,
            'description' => 'Monthly salary',
            'date' => '2026-02-24',
        ]);

        $response = $this->getJson("/api/v1/transactions/{$transaction->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $transaction->id,
                    'wallet_id' => $wallet->id,
                    'type' => 'income',
                    'amount' => '5000.50',
                    'description' => 'Monthly salary',
                ],
            ]);
    }

    /**
     * Test viewing non-existent transaction returns 404
     */
    public function test_cannot_view_non_existent_transaction(): void
    {
        $response = $this->getJson('/api/v1/transactions/invalid-id');

        $response->assertStatus(404);
    }

    /**
     * Test balance calculation with multiple transactions
     */
    public function test_balance_calculation_with_multiple_transactions(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet = $user->wallets()->create(['name' => 'Personal']);

        // Add 1000 income
        $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 1000,
            'date' => '2026-02-20',
        ]);

        // Add 500 income
        $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 500,
            'date' => '2026-02-21',
        ]);

        // Add 200 expense
        $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'expense',
            'amount' => 200,
            'date' => '2026-02-22',
        ]);

        // Add 100 expense
        $this->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'type' => 'expense',
            'amount' => 100,
            'date' => '2026-02-24',
        ]);

        $response = $this->getJson("/api/v1/wallets/{$wallet->id}");

        // Expected: (1000 + 500) - (200 + 100) = 1200
        $response->assertJson([
            'wallet' => [
                'balance' => 1200,
            ],
        ]);
    }
}
