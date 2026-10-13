<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'label',
        'sort_order',
    ];
}
