<?php

declare(strict_types=1);

namespace App\Http\Resources\Catalog;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Version "liste" d'un produit (cartes du catalogue). @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => (float) $this->price,
            'badge' => $this->badge,
            'is_accessory' => $this->is_accessory,
            'is_featured' => $this->is_featured,
            'excerpt' => $this->translated('excerpt'),
            'cover' => MediaUrl::from($this->images->first()?->path),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'color' => new ColorResource($this->whenLoaded('color')),
        ];
    }
}
