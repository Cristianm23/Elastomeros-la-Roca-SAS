<?php

namespace Database\Factories;

use App\Modules\Categories\Models\Category;
use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####')),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
