<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Transfère tout ce qu'un invité avait (panier, wishlist, commandes...) vers son compte.
 * Le service d'authentification n'a pas à connaître le détail : on ne modifie que cette classe.
 */
final class GuestDataMigrator
{
    public function __construct(
        private readonly CartService $cart,
        private readonly WishlistService $wishlist,
        private readonly OrderService $orders,
    ) {}

    public function migrate(?string $guestToken, int $userId): void
    {
        // Jeton absent ou aberrant : on ignore (une connexion ne doit jamais échouer à cause d'un panier).
        if ($guestToken === null || $guestToken === '' || strlen($guestToken) > 64) {
            return;
        }

        DB::transaction(function () use ($guestToken, $userId): void {
            $this->cart->mergeGuestCart($guestToken, $userId);
            $this->wishlist->mergeGuestWishlist($guestToken, $userId);
            $this->orders->adoptGuestOrders($guestToken, $userId);
        });
    }
}
