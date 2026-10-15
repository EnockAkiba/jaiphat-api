<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'setting_id',
        'locale',
        'tagline',
        'shipping_note',
        'hours',
    ];
}
