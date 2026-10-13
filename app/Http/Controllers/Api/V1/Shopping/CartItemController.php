<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Shopping;

use App\DTOs\Shopping\AddToCartData;
use App\DTOs\Shopping\Shopper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\AddCartItemRequest;
use App\Http\Requests\Shopping\UpdateCartItemRequest;
use App\Http\Resources\Shopping\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartItemController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function store(AddCartItemRequest $request, Shopper $shopper): JsonResponse
    {
        $cart = $this->cart->addItem($shopper, AddToCartData::fromRequest($request));

        return (new CartResource($cart))->response()->setStatusCode(201);
    }

    public function update(UpdateCartItemRequest $request, Shopper $shopper, int $item): CartResource
    {
        return new CartResource(
            $this->cart->updateQuantity($shopper, $item, (int) $request->validated('quantity'))
        );
    }

    public function destroy(Shopper $shopper, int $item): CartResource
    {
        return new CartResource($this->cart->removeItem($shopper, $item));
    }
}
