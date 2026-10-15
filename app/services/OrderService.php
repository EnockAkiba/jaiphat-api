<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Shopping\Shopper;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final class OrderService
{
    /** @return Collection<int, Order> */
    public function listFor(Shopper $shopper): Collection
    {
        return Order::query()->where($shopper->ownerColumns())->with('items')->latest('id')->get();
    }

    /** Cherche DANS les commandes du client : celle d'un autre donne un 404. */
    public function findFor(Shopper $shopper, string $number): Order
    {
        return Order::query()->where($shopper->ownerColumns())->where('number', $number)->with('items')->firstOrFail();
    }

    /** @throws ValidationException */
    public function cancel(Shopper $shopper, string $number): Order
    {
        $order = $this->findFor($shopper, $number);

        if ($order->status === OrderStatus::Cancelled) {
            return $order;
        }
        if ($order->status !== OrderStatus::Pending) {
            throw ValidationException::withMessages(['order' => 'Cette commande ne peut plus être annulée en ligne. Contactez-nous.']);
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        return $order;
    }

    /** Côté boutique : fixe les frais de livraison (en centimes) et confirme la commande. */
    public function quoteShipping(string $number, int $shippingCents): Order
    {
        $order = Order::query()->where('number', $number)->with('items')->firstOrFail();

        if ($order->status !== OrderStatus::Pending) {
            throw ValidationException::withMessages(['order' => 'Seule une commande en attente peut recevoir des frais de livraison.']);
        }

        $order->update([
            'shipping_cents' => $shippingCents,
            'total_cents' => $order->subtotal_cents + $shippingCents,
            'status' => OrderStatus::Confirmed,
        ]);

        return $order;
    }

    /** À la connexion / l'inscription : les commandes passées en invité rejoignent le compte. */
    public function adoptGuestOrders(string $guestToken, int $userId): void
    {
        Order::query()
            ->where('session_id', $guestToken)
            ->whereNull('user_id')
            ->update(['user_id' => $userId, 'session_id' => null]);
    }
}
