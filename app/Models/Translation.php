<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Language;
use App\Models\Tag;

class Translation extends Model
{
    protected $fillable = ['key', 'value', 'language_id'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

}
