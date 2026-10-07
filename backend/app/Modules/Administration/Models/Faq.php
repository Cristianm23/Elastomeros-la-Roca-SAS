<?php
namespace App\Modules\Administration\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Faq extends Model { use SoftDeletes; protected $fillable = ['question','answer','is_active','sort_order']; protected $casts = ['is_active'=>'boolean']; }
