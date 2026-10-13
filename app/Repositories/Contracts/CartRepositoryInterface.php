<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Shopping\Shopper;
use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    /** Panier du client avec toutes les relations pour l'affichage, ou null s'il n'en a pas encore. */
    public function findFor(Shopper $shopper): ?Cart;

    public function firstOrCreateFor(Shopper $shopper): Cart;

    /** Un panier vide, non enregistré (pour répondre à GET /cart sans créer de ligne en base). */
    public function emptyCart(): Cart;

    public function findLine(Cart $cart, int $productId, string $size): ?CartItem;

    /** Une ligne du panier, uniquement si elle appartient à CE panier. */
    public function findItem(Cart $cart, int $itemId): ?CartItem;

    public function saveLine(Cart $cart, int $productId, string $size, int $quantity): CartItem;

    public function updateQuantity(CartItem $item, int $quantity): void;

    public function deleteItem(CartItem $item): void;

    public function clear(Cart $cart): void;
}
