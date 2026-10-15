<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBandItemTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'home_band_item_id',
        'locale',
        'title',
        'text',
    ];
}
