<?php

namespace App\Modules\Categories\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Categories\Resources\CategoryResource;
use App\Modules\Categories\Services\CategoryCatalogService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryCatalogService $catalog) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->catalog->paginate($request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ])));
    }

    public function show(string $slug): CategoryResource
    {
        return new CategoryResource($this->catalog->findPublicBySlug($slug));
    }
}
