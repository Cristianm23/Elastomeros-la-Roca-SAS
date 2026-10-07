<?php

namespace App\Modules\Quotes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\QuoteFactory;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['public_reference', 'user_id', 'company_name', 'contact_name', 'email', 'phone', 'message', 'status', 'source_ip'];
    protected static function newFactory(): QuoteFactory { return QuoteFactory::new(); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(QuoteItem::class); }
}
