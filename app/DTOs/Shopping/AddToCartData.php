<?php

declare(strict_types=1);

namespace App\DTOs\Shopping;

use App\Http\Requests\Shopping\AddCartItemRequest;

final readonly class AddToCartData
{
    public function __construct(
        public int $productId,
        public string $size,
        public int $quantity = 1,
    ) {}

    public static function fromRequest(AddCartItemRequest $request): self
    {
        $data = $request->validated();

        return new self(
            productId: (int) $data['product_id'],
            size: (string) $data['size'],
            quantity: (int) ($data['quantity'] ?? 1),
        );
    }
}
