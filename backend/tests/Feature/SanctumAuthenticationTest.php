<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SanctumAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_login_and_access_protected_endpoint_with_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'secret-password',
            'device_name' => 'test-suite',
        ])->assertOk()->json('data.token');

        $this->withHeader('Authorization', "Bearer {$login}")
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'user@example.com', 'password' => Hash::make('secret-password')]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_protected_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
}
