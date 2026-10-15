<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveColorRequest;
use App\Http\Resources\Admin\AdminColorResource;
use App\Services\ColorAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ColorController extends Controller
{
    public function __construct(private readonly ColorAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminColorResource::collection($this->service->all());
    }

    public function store(SaveColorRequest $request): JsonResponse
    {
        return (new AdminColorResource($this->service->save($request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveColorRequest $request, int $color): AdminColorResource
    {
        return new AdminColorResource($this->service->save($request->validated(), $color));
    }

    public function destroy(int $color): JsonResponse
    {
        $this->service->delete($color);

        return response()->json(['data' => null]);
    }
}
