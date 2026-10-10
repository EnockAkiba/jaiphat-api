<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_detail_image',
        'hero_image',
        'hero_primary_to',
        'salon_image',
        'hero_secondary_to',
        'stat_brides',
        'stat_rating',
        'stat_reviews',
    ];
}
