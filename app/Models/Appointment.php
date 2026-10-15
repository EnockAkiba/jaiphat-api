<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'party',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'status' => AppointmentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
