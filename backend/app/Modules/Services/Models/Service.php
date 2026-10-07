<?php

namespace App\Modules\Services\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\ServiceFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function newFactory(): ServiceFactory { return ServiceFactory::new(); }
}
