<?php

namespace App\Modules\Products\Models;

use App\Modules\Categories\Models\Category;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function newFactory(): ProductFactory { return ProductFactory::new(); }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function specifications(): HasMany { return $this->hasMany(ProductSpecification::class)->orderBy('sort_order'); }
}
