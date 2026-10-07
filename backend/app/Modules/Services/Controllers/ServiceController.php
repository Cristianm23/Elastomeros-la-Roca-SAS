<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Services\Services\ServiceCatalogService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    public function __construct(private readonly ServiceCatalogService $catalog) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ServiceResource::collection($this->catalog->paginate($request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ])));
    }

    public function show(string $slug): ServiceResource
    {
        return new ServiceResource($this->catalog->findPublicBySlug($slug));
    }
}
