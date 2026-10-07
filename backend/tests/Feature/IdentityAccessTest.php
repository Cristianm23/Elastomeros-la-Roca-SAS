<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Users\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class IdentityAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_registration_logs_in_customer_with_required_profile_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Laura Méndez',
            'email' => 'laura@example.com',
            'phone' => '+57 300 111 2233',
            'company_name' => 'Roca Industrial SAS',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
            'privacy_accepted' => true,
            'device_name' => 'test-suite',
        ]);

        $response->assertCreated()->assertJsonPath('data.user.email', 'laura@example.com');
        $this->assertDatabaseHas('users', [
            'email' => 'laura@example.com',
            'company_name' => 'Roca Industrial SAS',
        ]);
        $this->assertDatabaseHas('roles', ['slug' => 'customer']);
    }

    public function test_registration_requires_privacy_acceptance_and_password_confirmation(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Laura Méndez',
            'email' => 'laura@example.com',
            'phone' => '+57 300 111 2233',
            'company_name' => 'Roca Industrial SAS',
            'password' => 'StrongPass123',
            'password_confirmation' => 'different',
            'privacy_accepted' => false,
        ])->assertStatus(422)->assertJsonValidationErrors(['password', 'privacy_accepted']);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('StrongPass123')]);
        $token = $user->createToken('test-suite')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_admin_permission_endpoint(): void
    {
        $user = User::factory()->create();
        $user->roles()->sync([Role::query()->where('slug', 'customer')->value('id')]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/access-check')
            ->assertForbidden();
    }

    public function test_admin_can_access_permission_endpoint(): void
    {
        $user = User::factory()->create();
        $user->roles()->sync([Role::query()->where('slug', 'admin')->value('id')]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/access-check')
            ->assertOk();
    }

    public function test_password_recovery_sends_reset_notification_without_exposing_account_state(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'recover@example.com']);

        $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('data.message', 'Si el correo existe, recibirás instrucciones para restablecer tu contraseña.');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
