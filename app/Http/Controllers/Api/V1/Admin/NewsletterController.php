<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminSubscriberResource;
use App\Services\InboxAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsletterController extends Controller
{
    public function __construct(private readonly InboxAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminSubscriberResource::collection($this->service->subscribers());
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->removeSubscriber($id);

        return response()->json(['data' => null]);
    }
}