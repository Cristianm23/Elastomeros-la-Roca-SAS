<?php

namespace App\Modules\Products\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image) => [
                'path' => $image->path,
                'alt_text' => $image->alt_text,
                'is_primary' => $image->is_primary,
            ])->values()),
            'specifications' => $this->whenLoaded('specifications', fn () => $this->specifications->map(fn ($specification) => [
                'name' => $specification->name,
                'value' => $specification->value,
                'unit' => $specification->unit,
            ])->values()),
        ];
    }
}
