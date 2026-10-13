<?php

declare(strict_types=1);

namespace App\Http\Resources\Shopping;

use App\Http\Resources\Catalog\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Wishlist */
class WishlistResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'count' => $this->products->count(),
            'products' => ProductResource::collection($this->products),
        ];
    }
}
