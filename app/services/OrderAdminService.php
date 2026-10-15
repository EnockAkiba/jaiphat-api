<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class OrderAdminService
{
    /** statut du front => statut de l'API */
    private const FROM_FRONT = [
        'pending' => OrderStatus::Pending,
        'confirmed' => OrderStatus::Confirmed,
        'fulfilled' => OrderStatus::Delivered,
        'cancelled' => OrderStatus::Cancelled,
    ];

    /** @return Collection<int, Order> */
    public function all(): Collection
    {
        return Order::query()->with('items')->latest()->limit(500)->get();
    }

    public function update(string $number, array $data): Order
    {
        return DB::transaction(function () use ($number, $data): Order {
            $order = Order::query()->where('number', $number)->lockForUpdate()->firstOrFail();

            if (isset($data['status'])) {
                $order->status = self::FROM_FRONT[$data['status']];
            }

            if (isset($data['shippingFee'])) {
                $order->shipping_cents = (int) round(((float) $data['shippingFee']) * 100);
                $order->total_cents = $order->subtotal_cents + $order->shipping_cents;
            }

            $order->save();

            return $order->load('items');
        });
    }
}