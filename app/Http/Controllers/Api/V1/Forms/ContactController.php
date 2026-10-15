<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forms\SendContactRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private readonly ContactService $contacts) {}

    public function store(SendContactRequest $request): JsonResponse
    {
        $this->contacts->send($request->safe()->only(['name', 'email', 'message']));

        return response()->json(['message' => 'Message envoyé. Nous vous répondrons très vite.'], 201);
    }
}
