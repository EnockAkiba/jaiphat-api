<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Shopping\Shopper;
use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class WishlistService
{
    public function __construct(private readonly WishlistRepositoryInterface $wishlists) {}

    public function show(Shopper $shopper): Wishlist
    {
        return $this->wishlists->findFor($shopper) ?? $this->wishlists->emptyWishlist();
    }

    public function add(Shopper $shopper, int $productId): Wishlist
    {
        $this->wishlists->addProduct($this->wishlists->firstOrCreateFor($shopper), $productId);

        return $this->show($shopper);
    }

    public function remove(Shopper $shopper, int $productId): Wishlist
    {
        if ($wishlist = $this->wishlists->findFor($shopper)) {
            $this->wishlists->removeProduct($wishlist, $productId);
        }

        return $this->show($shopper);
    }

    /** Fusionne la wishlist d'un invité dans celle d'un utilisateur, puis supprime celle de l'invité. */
    public function mergeGuestWishlist(string $guestToken, int $userId): void
    {
        $guestWishlist = $this->wishlists->findBareFor(new Shopper(guestToken: $guestToken));

        if ($guestWishlist === null) {
            return;
        }

        DB::transaction(function () use ($guestWishlist, $userId): void {
            $productIds = $this->wishlists->productIds($guestWishlist);

            if ($productIds !== []) {
                $userWishlist = $this->wishlists->firstOrCreateFor(new Shopper(userId: $userId));

                foreach ($productIds as $productId) {
                    $this->wishlists->addProduct($userWishlist, $productId);
                }
            }

            $this->wishlists->delete($guestWishlist);
        });
    }
}
