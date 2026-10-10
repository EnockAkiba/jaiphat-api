<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'public_id',
        'user_id',
        'name',
        'email',
        'phone',
        'date',
        'time',
        'notes',
        'status',
    ];

}
