<?php

namespace App\Modules\Users\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users(): BelongsToMany { return $this->belongsToMany(User::class); }
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class); }

    public function grants(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists()
            || $this->permissions()->where('slug', '*')->exists();
    }
}
