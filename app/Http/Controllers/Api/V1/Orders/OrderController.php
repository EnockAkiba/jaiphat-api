<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Orders;

use App\DTOs\Orders\PlaceOrderData;
use App\DTOs\Shopping\Shopper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\PlaceOrderRequest;
use App\Http\Resources\Orders\OrderResource;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly OrderService $orders,
    ) {}

    public function index(Shopper $shopper): AnonymousResourceCollection
    {
        return OrderResource::collection($this->orders->listFor($shopper));
    }

    public function store(PlaceOrderRequest $request, Shopper $shopper): JsonResponse
    {
        $order = $this->checkout->place($shopper, PlaceOrderData::fromRequest($request));

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(Shopper $shopper, string $number): OrderResource
    {
        return new OrderResource($this->orders->findFor($shopper, $number));
    }

    public function cancel(Shopper $shopper, string $number): OrderResource
    {
        return new OrderResource($this->orders->cancel($shopper, $number));
    }
}
