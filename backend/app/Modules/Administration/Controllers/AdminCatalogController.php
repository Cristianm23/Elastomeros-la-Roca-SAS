<?php

namespace App\Modules\Administration\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Administration\Services\AuditService;
use App\Modules\Categories\Models\Category;
use App\Modules\Products\Models\Product;
use App\Modules\Services\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCatalogController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(string $type): JsonResponse
    {
        $model = $this->model($type);
        return response()->json(['data' => $model::latest()->paginate(20)]);
    }

    public function save(Request $request, string $type, ?int $id = null): JsonResponse
    {
        $model = $this->model($type);
        $rules = match ($type) {
            'products' => ['name' => ['required', 'string', 'max:255'], 'category_id' => ['required', 'exists:categories,id'], 'slug' => ['nullable', 'string', 'max:255'], 'short_description' => ['nullable', 'string', 'max:500'], 'description' => ['nullable', 'string'], 'is_active' => ['boolean']],
            'categories' => ['name' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'is_active' => ['boolean']],
            'services' => ['name' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'description' => ['required', 'string'], 'is_active' => ['boolean']],
            default => abort(404),
        };
        $data = $request->validate($rules); $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $item = ($id ?: $request->integer('id')) ? $model::findOrFail($id ?: $request->integer('id')) : new $model;
        $old = $item->exists ? $item->toArray() : null; $item->fill($data); $item->save();
        $this->audit->record($old ? "{$type}.updated" : "{$type}.created", $item, $old, $item->toArray(), $request);
        return response()->json(['data' => $item], $old ? 200 : 201);
    }

    public function delete(Request $request, string $type, int $id): JsonResponse
    {
        $model = $this->model($type); $item = $model::findOrFail($id); $old = $item->toArray(); $item->delete(); $this->audit->record("{$type}.deleted", $item, $old, null, $request);
        return response()->json([], 204);
    }

    private function model(string $type): string
    {
        return match ($type) { 'products' => Product::class, 'categories' => Category::class, 'services' => Service::class, default => abort(404) };
    }
}
