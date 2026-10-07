<?php

namespace App\Modules\Products\Services;

use App\Modules\Products\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCatalogService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'images', 'specifications'])
            ->where('is_active', true)
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            }))
            ->when($filters['category'] ?? null, fn ($query, $slug) => $query->whereHas('category', fn ($category) => $category->where('slug', $slug)))
            ->orderBy('name')
            ->paginate(min((int) ($filters['per_page'] ?? 12), 50));
    }

    public function findPublicBySlug(string $slug): Product
    {
        return Product::query()
            ->with(['category', 'images', 'specifications'])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
