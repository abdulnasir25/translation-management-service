<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locale extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
