<?php

namespace Tests\Feature;

use App\Modules\Products\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_requires_consent_and_valid_fields(): void
    {
        $this->postJson('/api/v1/contact', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'subject' => 'Consulta',
            'message' => 'Necesito información.',
        ])->assertStatus(422)->assertJsonValidationErrors(['consent']);
    }

    public function test_contact_message_is_created(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/contact', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'subject' => 'Consulta',
            'message' => 'Necesito información.',
            'consent' => true,
        ])->assertCreated()->assertJsonPath('data.status', 'unread');

        $this->assertDatabaseHas('contact_messages', ['email' => 'ana@example.com']);
        Notification::assertSentOnDemand(\App\Notifications\ContactReceivedNotification::class);
    }

    public function test_quote_is_created_with_items(): void
    {
        Notification::fake();
        Storage::fake('local');
        $product = Product::factory()->create();

        $this->postJson('/api/v1/quotes', [
            'company_name' => 'Industria SAS',
            'contact_name' => 'Carlos',
            'email' => 'carlos@example.com',
            'city' => 'Bogotá',
            'required_date' => now()->addDays(7)->toDateString(),
            'consent' => true,
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonCount(1, 'data.items');

        $this->assertDatabaseHas('quotes', ['email' => 'carlos@example.com']);
        $this->assertDatabaseHas('quote_items', ['product_id' => $product->id, 'quantity' => 2]);
        Notification::assertSentOnDemand(\App\Notifications\QuoteReceivedNotification::class);
    }

    public function test_quote_rejects_invalid_attachment_and_honeypot(): void
    {
        $response = $this->post('/api/v1/quotes', [
            'company_name' => 'Industria SAS',
            'contact_name' => 'Carlos',
            'email' => 'carlos@example.com',
            'city' => 'Bogotá',
            'required_date' => now()->addDays(7)->toDateString(),
            'consent' => true,
            'website' => 'bot',
            'items' => [['description' => 'Sello especial', 'quantity' => 2]],
            'attachment' => \Illuminate\Http\UploadedFile::fake()->create('malware.exe', 11_000, 'application/octet-stream'),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['website', 'attachment']);
    }
}
