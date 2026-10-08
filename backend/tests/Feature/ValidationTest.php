<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_privacy_and_password_confirmation(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'phone' => '3001234567',
            'company_name' => 'Empresa',
            'password' => 'Password123',
            'password_confirmation' => 'Different123',
        ])->assertStatus(422)->assertJsonValidationErrors(['password', 'privacy_accepted']);
    }

    public function test_quote_rejects_invalid_status_and_unknown_product(): void
    {
        $this->postJson('/api/v1/quotes', [
            'company_name' => 'Empresa',
            'contact_name' => 'Cliente',
            'email' => 'cliente@example.com',
            'city' => 'Bogotá',
            'required_date' => now()->addDay()->toDateString(),
            'consent' => true,
            'items' => [['product_id' => 999999, 'quantity' => 1]],
        ])->assertStatus(422)->assertJsonValidationErrors(['items.0.product_id']);
    }
}
