<?php

namespace App\Modules\Categories\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'products' => $this->whenLoaded('products', fn () => $this->products->map(fn ($product) => [
                'name' => $product->name,
                'slug' => $product->slug,
                'short_description' => $product->short_description,
            ])->values()),
        ];
    }
}
