<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeServiceTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'home_service_id',
        'locale',
        'title',
        'text',
    ];
}
