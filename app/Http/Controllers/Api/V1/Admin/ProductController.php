<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Http\Resources\Admin\AdminProductResource;
use App\Services\ProductAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(private readonly ProductAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminProductResource::collection($this->service->all());
    }

    public function store(SaveProductRequest $request): JsonResponse
    {
        return (new AdminProductResource($this->service->save($request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveProductRequest $request, int $product): AdminProductResource
    {
        return new AdminProductResource($this->service->save($request->validated(), $product));
    }

    public function destroy(int $product): JsonResponse
    {
        $this->service->delete($product);

        return response()->json(['data' => null]);
    }
}
