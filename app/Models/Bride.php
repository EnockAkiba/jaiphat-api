<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bride extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date',
        'time',
        'service',
        'message',
    ];
    
}
