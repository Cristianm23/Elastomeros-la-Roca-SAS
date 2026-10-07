<?php

namespace App\Modules\Categories\Models;

use App\Modules\Products\Models\Product;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function newFactory(): CategoryFactory { return CategoryFactory::new(); }

    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
}
