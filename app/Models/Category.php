<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where('id', $value)
            ->orWhere('slug', Str::slug((string) $value))
            ->first();
    }
}
