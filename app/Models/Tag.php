<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Translation;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function translations()
    {
        return $this->belongsToMany(Translation::class);
    }

}
