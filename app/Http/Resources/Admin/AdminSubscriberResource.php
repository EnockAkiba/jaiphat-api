<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin NewsletterSubscriber */
class AdminSubscriberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'locale' => $this->locale,
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}