<?php

namespace App\Modules\Services\Services;

use App\Modules\Services\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceCatalogService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Service::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')
            ->paginate(min((int) ($filters['per_page'] ?? 20), 50));
    }

    public function findPublicBySlug(string $slug): Service
    {
        return Service::query()->where('is_active', true)->where('slug', $slug)->firstOrFail();
    }
}
