<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Shopping\Shopper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LoadsProductCards;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCartRepository implements CartRepositoryInterface
{
    use LoadsProductCards;

    public function findFor(Shopper $shopper): ?Cart
    {
        return Cart::query()
            ->where($shopper->ownerColumns())
            ->with($this->productCardRelations('items.product'))
            ->first();
    }

    public function findWithItems(Shopper $shopper): ?Cart
    {
        return Cart::query()->where($shopper->ownerColumns())->with('items')->first();
    }

    public function firstOrCreateFor(Shopper $shopper): Cart
    {
        return Cart::query()->firstOrCreate($shopper->ownerColumns());
    }

    public function emptyCart(): Cart
    {
        return (new Cart)->setRelation('items', new Collection);
    }

    public function findLine(Cart $cart, int $productId, string $size): ?CartItem
    {
        return $cart->items()->where('product_id', $productId)->where('size', $size)->first();
    }

    public function findItem(Cart $cart, int $itemId): ?CartItem
    {
        return $cart->items()->whereKey($itemId)->first();
    }

    public function saveLine(Cart $cart, int $productId, string $size, int $quantity): CartItem
    {
        return $cart->items()->updateOrCreate(
            ['product_id' => $productId, 'size' => $size],
            ['qty' => $quantity],
        );
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        $item->update(['qty' => $quantity]);
    }

    public function deleteItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function delete(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->delete();
    }
}
