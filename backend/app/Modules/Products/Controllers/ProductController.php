<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Resources\ProductResource;
use App\Modules\Products\Services\ProductCatalogService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(private readonly ProductCatalogService $catalog) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection($this->catalog->paginate($request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ])));
    }

    public function show(string $slug): ProductResource
    {
        return new ProductResource($this->catalog->findPublicBySlug($slug));
    }
}
