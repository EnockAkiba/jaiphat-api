<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'brand',
        'phone',
        'phone_href',
        'email',
        'address',
        'free_shipping',
        'instagram',
    ];
}
