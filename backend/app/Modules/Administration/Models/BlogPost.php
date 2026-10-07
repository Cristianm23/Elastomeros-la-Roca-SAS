<?php
namespace App\Modules\Administration\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlogPost extends Model { use SoftDeletes; protected $fillable = ['title','slug','excerpt','body','status','published_at','author_id']; protected $casts = ['published_at'=>'datetime']; }
