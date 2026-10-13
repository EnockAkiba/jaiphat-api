<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'color_id',
        'locale',
        'name',
    ];
}
