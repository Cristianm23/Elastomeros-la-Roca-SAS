<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Analytics\Models\AnalyticsEvent;
use App\Modules\Users\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_analytics_accepts_allowlisted_event_without_sensitive_data(): void
    {
        $response = $this->postJson('/api/v1/analytics/events', [
            'event_name' => 'product_view',
            'path' => '/productos/sellos-hidraulicos',
            'product_slug' => 'sellos-hidraulicos',
            'properties' => ['source' => 'catalog', 'email' => 'should-be-ignored'],
        ]);

        $response->assertStatus(202);
        $this->assertDatabaseHas('analytics_events', [
            'event_name' => 'product_view',
            'product_slug' => 'sellos-hidraulicos',
        ]);
        $this->assertDatabaseMissing('analytics_events', ['properties->email' => 'should-be-ignored']);
    }

    public function test_admin_can_read_analytics_summary(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->where('slug', 'admin')->firstOrFail());
        AnalyticsEvent::create(['event_name' => 'product_view', 'product_slug' => 'sello', 'properties' => null]);

        $this->withToken($admin->createToken('test')->plainTextToken)
            ->getJson('/api/v1/admin/analytics')
            ->assertOk()
            ->assertJsonPath('data.products.0.product_slug', 'sello');
    }
}
