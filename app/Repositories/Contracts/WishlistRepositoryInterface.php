<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Shopping\Shopper;
use App\Models\Wishlist;

interface WishlistRepositoryInterface
{
    public function findFor(Shopper $shopper): ?Wishlist;

    /** Sans relations chargées : pour les opérations internes (fusion). */
    public function findBareFor(Shopper $shopper): ?Wishlist;

    public function firstOrCreateFor(Shopper $shopper): Wishlist;

    public function emptyWishlist(): Wishlist;

    /** Idempotent : ajouter deux fois le même produit ne crée pas de doublon. */
    public function addProduct(Wishlist $wishlist, int $productId): void;

    public function removeProduct(Wishlist $wishlist, int $productId): void;

    /** @return list<int> */
    public function productIds(Wishlist $wishlist): array;

    /** Supprime la wishlist ET ses liens vers les produits. */
    public function delete(Wishlist $wishlist): void;
}
