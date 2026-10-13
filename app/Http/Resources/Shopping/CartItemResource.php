<?php

declare(strict_types=1);

namespace App\Http\Resources\Shopping;

use App\Http\Resources\Catalog\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CartItem */
class CartItemResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'quantity' => $this->qty,
            'unit_price' => (float) $this->product->price,
            'line_total' => round($this->lineTotalCents() / 100, 2),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
