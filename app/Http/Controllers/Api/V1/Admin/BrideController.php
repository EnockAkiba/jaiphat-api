<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBrideRequest;
use App\Http\Resources\Admin\AdminBrideResource;
use App\Services\BrideAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrideController extends Controller
{
    public function __construct(private readonly BrideAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminBrideResource::collection($this->service->all());
    }

    public function store(SaveBrideRequest $request): JsonResponse
    {
        return (new AdminBrideResource($this->service->save($request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveBrideRequest $request, int $bride): AdminBrideResource
    {
        return new AdminBrideResource($this->service->save($request->validated(), $bride));
    }

    public function destroy(int $bride): JsonResponse
    {
        $this->service->delete($bride);

        return response()->json(['data' => null]);
    }
}