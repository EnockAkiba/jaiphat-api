<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Shopping;

use App\DTOs\Shopping\Shopper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shopping\CartResource;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function show(Shopper $shopper): CartResource
    {
        return new CartResource($this->cart->show($shopper));
    }

    /** Vide le panier. */
    public function destroy(Shopper $shopper): CartResource
    {
        return new CartResource($this->cart->clear($shopper));
    }
}
