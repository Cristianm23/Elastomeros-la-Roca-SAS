<?php

namespace App\Modules\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'company_name', 'email', 'phone', 'subject', 'message', 'status', 'consent_at'];
    protected $casts = ['consent_at' => 'datetime'];
}
