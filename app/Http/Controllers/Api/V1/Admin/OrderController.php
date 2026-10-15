<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Resources\Admin\AdminOrderResource;
use App\Services\OrderAdminService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminOrderResource::collection($this->service->all());
    }

    public function update(UpdateOrderRequest $request, string $number): AdminOrderResource
    {
        return new AdminOrderResource($this->service->update($number, $request->validated()));
    }
}