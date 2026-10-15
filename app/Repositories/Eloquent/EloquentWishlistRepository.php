<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Shopping\Shopper;
use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LoadsProductCards;
use Illuminate\Database\Eloquent\Collection;

final class EloquentWishlistRepository implements WishlistRepositoryInterface
{
    use LoadsProductCards;

    public function findFor(Shopper $shopper): ?Wishlist
    {
        return Wishlist::query()
            ->where($shopper->ownerColumns())
            ->with($this->productCardRelations('products'))
            ->first();
    }

    public function findBareFor(Shopper $shopper): ?Wishlist
    {
        return Wishlist::query()->where($shopper->ownerColumns())->first();
    }

    public function firstOrCreateFor(Shopper $shopper): Wishlist
    {
        return Wishlist::query()->firstOrCreate($shopper->ownerColumns());
    }

    public function emptyWishlist(): Wishlist
    {
        return (new Wishlist)->setRelation('products', new Collection);
    }

    public function addProduct(Wishlist $wishlist, int $productId): void
    {
        $wishlist->products()->syncWithoutDetaching([$productId]);
    }

    public function removeProduct(Wishlist $wishlist, int $productId): void
    {
        $wishlist->products()->detach($productId);
    }

    public function productIds(Wishlist $wishlist): array
    {
        return $wishlist->products()->pluck('products.id')->map(fn ($id) => (int) $id)->all();
    }

    public function delete(Wishlist $wishlist): void
    {
        $wishlist->products()->detach();
        $wishlist->delete();
    }
}
