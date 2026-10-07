<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Quotes\Models\Quote;
use App\Modules\Users\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_only_read_own_quote(): void
    {
        $owner = User::factory()->create();
        $owner->roles()->sync([Role::where('slug', 'customer')->value('id')]);
        $other = User::factory()->create();
        $other->roles()->sync([Role::where('slug', 'customer')->value('id')]);
        $quote = Quote::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner, 'sanctum')->getJson("/api/v1/quotes/{$quote->public_reference}")->assertOk();
        $this->actingAs($other, 'sanctum')->getJson("/api/v1/quotes/{$quote->public_reference}")->assertForbidden();
    }

    public function test_employee_with_quotes_view_can_read_quote(): void
    {
        $employee = User::factory()->create();
        $employee->roles()->sync([Role::where('slug', 'employee')->value('id')]);
        $quote = Quote::factory()->create();

        $this->actingAs($employee, 'sanctum')->getJson("/api/v1/quotes/{$quote->public_reference}")->assertOk();
    }
}
