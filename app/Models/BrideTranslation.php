<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrideTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'bride_id',
        'locale',
        'quote',
    ];
}
