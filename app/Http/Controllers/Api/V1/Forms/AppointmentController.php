<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Forms;

use App\DTOs\Forms\BookAppointmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forms\AvailabilityRequest;
use App\Http\Requests\Forms\BookAppointmentRequest;
use App\Http\Resources\Forms\AppointmentResource;
use App\Services\AppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointments) {}

    public function availability(AvailabilityRequest $request): JsonResponse
    {
        $date = CarbonImmutable::createFromFormat('Y-m-d', $request->validated('date'));

        return response()->json(['data' => [
            'date' => $date->toDateString(),
            'slots' => $this->appointments->availability($date),
        ]]);
    }

    public function store(BookAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointments->book(BookAppointmentData::fromRequest($request));

        return (new AppointmentResource($appointment))->response()->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return AppointmentResource::collection($this->appointments->forUser($request->user()->id));
    }

    public function cancel(Request $request, string $publicId): AppointmentResource
    {
        return new AppointmentResource($this->appointments->cancel($request->user()->id, $publicId));
    }
}
