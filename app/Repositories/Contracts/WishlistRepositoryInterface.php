<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Shopping\Shopper;
use App\Models\Wishlist;

interface WishlistRepositoryInterface
{
    public function findFor(Shopper $shopper): ?Wishlist;

    public function firstOrCreateFor(Shopper $shopper): Wishlist;

    public function emptyWishlist(): Wishlist;

    /** Idempotent : ajouter deux fois le même produit ne crée pas de doublon. */
    public function addProduct(Wishlist $wishlist, int $productId): void;

    public function removeProduct(Wishlist $wishlist, int $productId): void;
}
