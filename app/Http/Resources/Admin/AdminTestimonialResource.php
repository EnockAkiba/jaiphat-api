<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\Concerns\BuildsTranslationPairs;
use App\Models\Testimonial;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Testimonial */
class AdminTestimonialResource extends JsonResource
{
    use BuildsTranslationPairs;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => MediaUrl::from($this->avatar),
            'translations' => $this->pairs($this->translations, ['quote' => 'quote']),
        ];
    }
}