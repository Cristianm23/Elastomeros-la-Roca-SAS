<?php

namespace Tests\Feature;

use App\Modules\Categories\Models\Category;
use App\Modules\Products\Models\Product;
use App\Modules\Services\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_listed_and_filtered(): void
    {
        $category = Category::factory()->create(['slug' => 'sellos']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Sello hidráulico', 'slug' => 'sello-hidraulico']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Manguera industrial', 'slug' => 'manguera-industrial']);

        $response = $this->getJson('/api/v1/products?search=hidráulico');

        $response->assertOk()->assertJsonPath('data.0.slug', 'sello-hidraulico');
    }

    public function test_product_detail_is_resolved_by_slug(): void
    {
        $product = Product::factory()->create(['slug' => 'junta-especial']);

        $this->getJson("/api/v1/products/{$product->slug}")
            ->assertOk()
            ->assertJsonPath('data.slug', 'junta-especial');
    }

    public function test_services_are_publicly_listed(): void
    {
        Service::factory()->create(['slug' => 'vulcanizado']);

        $this->getJson('/api/v1/services')->assertOk()->assertJsonPath('data.0.slug', 'vulcanizado');
    }
}
