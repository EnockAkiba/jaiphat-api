<?php

declare(strict_types=1);

namespace App\Http\Resources\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $money = fn (?int $cents) => $cents === null ? null : round($cents / 100, 2);

        return [
            'number' => $this->number,
            'status' => $this->status->value,
            'payment_status' => $this->payment_status,
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ],
            'shipping_address' => [
                'address' => $this->address,
                'city' => $this->city,
                'district' => $this->district,
                'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
                'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
                'notes' => $this->notes,
            ],
            'items' => $this->items->map(fn ($i) => [
                'product_name' => $i->product_name,
                'size' => $i->size,
                'quantity' => $i->quantity,
                'unit_price' => $money($i->unit_price_cents),
                'line_total' => $money($i->line_total_cents),
            ])->values(),
            'subtotal' => $money($this->subtotal_cents),
            'shipping_fee' => $money($this->shipping_cents),  // null = à confirmer
            'shipping_pending' => $this->isShippingPending(),
            'total' => $money($this->total_cents),            // null tant que la livraison n'est pas chiffrée
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
