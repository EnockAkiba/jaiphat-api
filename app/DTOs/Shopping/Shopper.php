<?php

declare(strict_types=1);

namespace App\DTOs\Shopping;

/**
 * "Qui possède ce panier / cette wishlist ?" : un utilisateur connecté OU un invité (jeton).
 * Les services et repositories ne manipulent que cet objet, jamais la Request ni l'en-tête HTTP.
 */
final readonly class Shopper
{
    public function __construct(
        public ?int $userId = null,
        public ?string $guestToken = null,
    ) {
        if ($userId === null && $guestToken === null) {
            throw new \LogicException('Un Shopper doit avoir un userId ou un guestToken (le middleware IdentifyShopper est-il appliqué à la route ?).');
        }
    }

    /** Colonnes de la table (carts / wishlists) qui identifient ce client. */
    public function ownerColumns(): array
    {
        return $this->userId !== null
            ? ['user_id' => $this->userId]
            : ['session_id' => $this->guestToken];
    }
}
