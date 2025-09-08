<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    protected $fillable = [
        'translation_key_id',
        'locale_id',
        'content',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
