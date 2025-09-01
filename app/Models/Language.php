<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Translation;

class Language extends Model
{
    protected $fillable = ['locale', 'name', 'is_active'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

}
