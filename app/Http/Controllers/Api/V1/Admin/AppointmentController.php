<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminAppointmentResource;
use App\Services\InboxAdminService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function __construct(private readonly InboxAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminAppointmentResource::collection($this->service->appointments());
    }

    public function update(Request $request, string $id): AdminAppointmentResource
    {
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])]]);

        return new AdminAppointmentResource($this->service->setAppointmentStatus($id, $data['status']));
    }
}