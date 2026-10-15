<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveHomeRequest;
use App\Services\HomeAdminService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(private readonly HomeAdminService $service) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->service->get()]);
    }

    public function update(SaveHomeRequest $request): JsonResponse
    {
        return response()->json(['data' => $this->service->update($request->validated())]);
    }
}