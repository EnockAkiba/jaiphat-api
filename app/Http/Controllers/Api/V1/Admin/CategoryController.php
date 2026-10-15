<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Http\Resources\Admin\AdminCategoryResource;
use App\Services\CategoryAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminCategoryResource::collection($this->service->all());
    }

    public function store(SaveCategoryRequest $request): JsonResponse
    {
        return (new AdminCategoryResource($this->service->save($request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveCategoryRequest $request, int $category): AdminCategoryResource
    {
        return new AdminCategoryResource($this->service->save($request->validated(), $category));
    }

    public function destroy(int $category): JsonResponse
    {
        $this->service->delete($category);

        return response()->json(['data' => null]);
    }
}
