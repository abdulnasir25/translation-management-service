<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TranslationKey extends Model
{
    protected $fillable = [
        'key',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'translation_tags');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
