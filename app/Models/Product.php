<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'price',
        'category_id',
        'color_id',
        'is_accessory',
        'is_featured',
        'badge',
        'sort_order',
    ];

}
