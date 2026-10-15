<?php

declare(strict_types=1);

namespace App\Http\Resources\Forms;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date->format('Y-m-d'),
            'time' => substr((string) $this->time, 0, 5),
            'party' => $this->party,
            'notes' => $this->notes,
            'status' => $this->status->value,
        ];
    }
}
