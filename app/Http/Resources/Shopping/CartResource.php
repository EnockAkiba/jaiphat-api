<?php

declare(strict_types=1);

namespace App\Http\Resources\Shopping;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Cart */
class CartResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // null tant que l'invité n'a rien ajouté
            'items' => CartItemResource::collection($this->items),
            'items_count' => $this->itemsCount(),
            'subtotal' => round($this->subtotalCents() / 100, 2),
        ];
    }
}
