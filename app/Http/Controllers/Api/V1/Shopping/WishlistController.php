<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Shopping;

use App\DTOs\Shopping\Shopper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shopping\AddWishlistItemRequest;
use App\Http\Resources\Shopping\WishlistResource;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    public function __construct(private readonly WishlistService $wishlist) {}

    public function show(Shopper $shopper): WishlistResource
    {
        return new WishlistResource($this->wishlist->show($shopper));
    }

    public function store(AddWishlistItemRequest $request, Shopper $shopper): JsonResponse
    {
        $wishlist = $this->wishlist->add($shopper, (int) $request->validated('product_id'));

        return (new WishlistResource($wishlist))->response()->setStatusCode(201);
    }

    public function destroy(Shopper $shopper, int $product): WishlistResource
    {
        return new WishlistResource($this->wishlist->remove($shopper, $product));
    }
}
