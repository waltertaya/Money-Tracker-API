<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a user with valid data
     */
    public function test_can_create_user_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully.',
            ])
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'created_at'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test creating user fails with missing required fields
     */
    public function test_cannot_create_user_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test creating user fails with invalid email
     */
    public function test_cannot_create_user_with_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test creating user fails with duplicate email
     */
    public function test_cannot_create_user_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test creating user fails with short password
     */
    public function test_cannot_create_user_with_short_password(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test retrieving user profile with no wallets
     */
    public function test_can_view_user_profile_with_no_wallets(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
                'wallets' => [],
                'overall_balance' => 0,
            ]);
    }

    /**
     * Test retrieving user profile with wallets
     */
    public function test_can_view_user_profile_with_wallets(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $wallet1 = $user->wallets()->create(['name' => 'Personal']);
        $wallet2 = $user->wallets()->create(['name' => 'Business']);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                ],
                'wallets' => [
                    ['id' => $wallet1->id, 'name' => 'Personal', 'balance' => 0],
                    ['id' => $wallet2->id, 'name' => 'Business', 'balance' => 0],
                ],
                'overall_balance' => 0,
            ])
            ->assertJsonCount(2, 'wallets');
    }

    /**
     * Test retrieving non-existent user returns 404
     */
    public function test_cannot_view_non_existent_user(): void
    {
        $response = $this->getJson('/api/v1/users/invalid-id');

        $response->assertStatus(404);
    }
}
