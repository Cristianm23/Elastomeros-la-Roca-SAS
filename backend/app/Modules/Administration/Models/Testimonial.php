<?php
namespace App\Modules\Administration\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Testimonial extends Model { use SoftDeletes; protected $fillable = ['author','company','quote','is_active']; protected $casts = ['is_active'=>'boolean']; }
