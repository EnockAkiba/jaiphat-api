<?php

declare(strict_types=1);

namespace App\Http\Resources\Showcase;

use App\Models\Testimonial;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Testimonial */
class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => MediaUrl::from($this->avatar),
            'quote' => $this->translated('quote'),
        ];
    }
}
