<?php

declare(strict_types=1);

namespace App\Http\Resources\Catalog;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Color */
class ColorResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->translated('name'),
            'swatch' => $this->swatch,
            'image' => MediaUrl::from($this->image),
        ];
    }
}
