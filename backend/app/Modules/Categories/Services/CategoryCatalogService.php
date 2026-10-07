<?php

namespace App\Modules\Categories\Services;

use App\Modules\Categories\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryCatalogService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Category::query()
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(min((int) ($filters['per_page'] ?? 20), 50));
    }

    public function findPublicBySlug(string $slug): Category
    {
        return Category::query()
            ->with(['products' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
