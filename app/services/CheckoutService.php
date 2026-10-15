<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Orders\PlaceOrderData;
use App\DTOs\Shopping\Shopper;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Transforme le panier du client en commande, en une seule transaction. */
final class CheckoutService
{
    public function __construct(private readonly CartRepositoryInterface $carts) {}

    /** @throws ValidationException si le panier est vide */
    public function place(Shopper $shopper, PlaceOrderData $data): Order
    {
        return DB::transaction(function () use ($shopper, $data): Order {
            $cart = $this->carts->findFor($shopper);
            $cart?->loadMissing('items.product');

            if ($cart === null || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Votre panier est vide.']);
            }

            $order = Order::create([
                'number' => $this->newNumber(),
                'user_id' => $shopper->userId,
                'session_id' => $shopper->userId === null ? $shopper->guestToken : null,
                'customer_name' => $data->customerName,
                'customer_email' => $data->customerEmail,
                'customer_phone' => $data->customerPhone,
                'address' => $data->address,
                'city' => $data->city,
                'district' => $data->district,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'notes' => $data->notes,
                'subtotal_cents' => $cart->subtotalCents(),
                'shipping_cents' => null,   // fixé plus tard, selon la distance
                'total_cents' => null,
                'status' => OrderStatus::Pending,
                'payment_status' => 'unpaid',
            ]);

            foreach ($cart->items as $item) {
                $unit = (int) round((float) $item->product->price * 100);
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => (string) ($item->product->translated('name') ?? $item->product->slug),
                    'size' => $item->size,
                    'unit_price_cents' => $unit,
                    'quantity' => $item->qty,
                    'line_total_cents' => $unit * $item->qty,
                ]);
            }

            $this->carts->clear($cart);

            return $order->load('items');
        });
    }

    private function newNumber(): string
    {
        do {
            $number = 'JP-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
