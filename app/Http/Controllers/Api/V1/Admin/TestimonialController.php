<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTestimonialRequest;
use App\Http\Resources\Admin\AdminTestimonialResource;
use App\Services\TestimonialAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestimonialController extends Controller
{
    public function __construct(private readonly TestimonialAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminTestimonialResource::collection($this->service->all());
    }

    public function store(SaveTestimonialRequest $request): JsonResponse
    {
        return (new AdminTestimonialResource($this->service->save($request->validated())))->response()->setStatusCode(201);
    }

    public function update(SaveTestimonialRequest $request, int $testimonial): AdminTestimonialResource
    {
        return new AdminTestimonialResource($this->service->save($request->validated(), $testimonial));
    }

    public function destroy(int $testimonial): JsonResponse
    {
        $this->service->delete($testimonial);

        return response()->json(['data' => null]);
    }
}