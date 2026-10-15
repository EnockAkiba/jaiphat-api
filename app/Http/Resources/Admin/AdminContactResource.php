<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Contact */
class AdminContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'read' => $this->read_at !== null,
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}