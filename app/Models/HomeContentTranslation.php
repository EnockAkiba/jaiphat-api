<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContentTranslation extends Model
{
    protected $table = 'home_setting_translations';

    public $timestamps = false;

    protected $fillable = [
        'home_content_id',
        'locale',
        'hero_kicker',
        'hero_title',
        'hero_script',
        'hero_text',
        'band_title',
    ];
}
