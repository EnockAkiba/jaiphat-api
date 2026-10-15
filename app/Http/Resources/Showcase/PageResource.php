<?php

declare(strict_types=1);

namespace App\Http\Resources\Showcase;

use App\Models\Page;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Page */
class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'image' => MediaUrl::from($this->image),
            'secondary_image' => MediaUrl::from($this->secondary_image),
            'kicker' => $this->translated('kicker'),
            'title' => $this->translated('title'),
            'lede' => $this->translated('lede'),
            'paragraphs' => $this->translated('paragraphs') ?? [],
            'values' => $this->translated('values') ?? [],
        ];
    }
}
