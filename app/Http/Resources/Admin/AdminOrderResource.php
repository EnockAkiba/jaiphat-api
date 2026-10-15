<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class AdminOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $money = fn (?int $cents) => $cents === null ? null : round($cents / 100, 2);

        return [
            'id' => $this->number,
            'status' => match ($this->status) {
                OrderStatus::Delivered => 'fulfilled',
                OrderStatus::Shipped => 'confirmed',
                default => $this->status->value,
            },
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ],
            'shipping' => [
                'address' => $this->address,
                'city' => $this->city,
                'district' => $this->district,
                'notes' => $this->notes,
            ],
            'items' => $this->items->map(fn ($i) => [
                'productId' => $i->product_id,
                'name' => $i->product_name,
                'size' => $i->size,
                'qty' => $i->quantity,
                'price' => $money($i->unit_price_cents),
            ])->values(),
            'subtotal' => $money($this->subtotal_cents),
            'shippingFee' => $money($this->shipping_cents),
            'shippingPending' => $this->isShippingPending(),
            // tant que les frais ne sont pas chiffrés, on affiche le sous-total (shippingPending le précise)
            'total' => $money($this->total_cents ?? $this->subtotal_cents),
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}