<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Categories\Models\Category;
use App\Modules\Products\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_register_login_browse_quote_and_logout(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Cliente E2E',
            'email' => 'e2e@example.com',
            'phone' => '3001234567',
            'company_name' => 'Empresa E2E',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'privacy_accepted' => true,
        ])->assertCreated();

        $token = $register->json('data.token');
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->getJson('/api/v1/products')->assertOk();
        $this->getJson("/api/v1/products/{$product->slug}")->assertOk();
        $quote = $this->withToken($token)->postJson('/api/v1/quotes', [
            'company_name' => 'Empresa E2E',
            'contact_name' => 'Cliente E2E',
            'email' => 'e2e@example.com',
            'city' => 'Bogotá',
            'required_date' => now()->addDay()->toDateString(),
            'consent' => true,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertCreated();

        self::assertSame('pending', $quote->json('data.status'));
        $this->withToken($token)->getJson('/api/v1/auth/me')->assertOk();
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
}
