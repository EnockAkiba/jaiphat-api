<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Shopping\Shopper;
use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;

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
}
