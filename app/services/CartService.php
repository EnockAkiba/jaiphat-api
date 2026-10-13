<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Shopping\AddToCartData;
use App\DTOs\Shopping\Shopper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Règles métier du panier. Chaque méthode publique renvoie le panier À JOUR :
 * le frontend n'a pas besoin de refaire un GET /cart après chaque action.
 */
final class CartService
{
    /** Quantité maximale d'un même article (même produit + même taille). */
    public const MAX_QUANTITY_PER_LINE = 10;

    public function __construct(private readonly CartRepositoryInterface $carts) {}

    public function show(Shopper $shopper): Cart
    {
        return $this->carts->findFor($shopper) ?? $this->carts->emptyCart();
    }

    public function addItem(Shopper $shopper, AddToCartData $data): Cart
    {
        DB::transaction(function () use ($shopper, $data): void {
            $cart = $this->carts->firstOrCreateFor($shopper);
            $existing = $this->carts->findLine($cart, $data->productId, $data->size);

            $quantity = ($existing?->qty ?? 0) + $data->quantity;
            $this->assertQuantityAllowed($quantity);

            $this->carts->saveLine($cart, $data->productId, $data->size, $quantity);
        });

        return $this->show($shopper);
    }

    /** @throws ModelNotFoundException si la ligne n'existe pas OU n'appartient pas à ce client */
    public function updateQuantity(Shopper $shopper, int $itemId, int $quantity): Cart
    {
        $this->assertQuantityAllowed($quantity);
        $this->carts->updateQuantity($this->ownedItem($shopper, $itemId), $quantity);

        return $this->show($shopper);
    }

    /** @throws ModelNotFoundException */
    public function removeItem(Shopper $shopper, int $itemId): Cart
    {
        $this->carts->deleteItem($this->ownedItem($shopper, $itemId));

        return $this->show($shopper);
    }

    public function clear(Shopper $shopper): Cart
    {
        if ($cart = $this->carts->findFor($shopper)) {
            $this->carts->clear($cart);
        }

        return $this->show($shopper);
    }

    /**
     * Sécurité : on cherche la ligne DANS le panier du client. Un client ne peut donc jamais
     * modifier la ligne d'un autre (il reçoit un 404, comme si elle n'existait pas).
     */
    private function ownedItem(Shopper $shopper, int $itemId): CartItem
    {
        $cart = $this->carts->findFor($shopper);

        return ($cart ? $this->carts->findItem($cart, $itemId) : null)
            ?? throw (new ModelNotFoundException)->setModel(CartItem::class, [$itemId]);
    }

    private function assertQuantityAllowed(int $quantity): void
    {
        if ($quantity < 1 || $quantity > self::MAX_QUANTITY_PER_LINE) {
            throw ValidationException::withMessages([
                'quantity' => 'La quantité par article doit être comprise entre 1 et '.self::MAX_QUANTITY_PER_LINE.'.',
            ]);
        }
    }
}
