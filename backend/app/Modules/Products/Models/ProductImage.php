<?php

namespace App\Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'path', 'alt_text', 'sort_order', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
