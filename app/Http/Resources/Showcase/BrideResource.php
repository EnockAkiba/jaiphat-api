<?php

declare(strict_types=1);

namespace App\Http\Resources\Showcase;

use App\Models\Bride;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Bride */
class BrideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gown_name' => $this->gown_name,
            'image' => MediaUrl::from($this->image),
            'quote' => $this->translated('quote'),
            'product_slug' => $this->product?->slug,
        ];
    }
}
