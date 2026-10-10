<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bride extends Model
{
    protected $fillable = [
        'name',
        'gown_name',
        'product_id',
        'image',
        'sort_order',
    ];
}
