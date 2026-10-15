<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forms\SubscribeNewsletterRequest;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterService $newsletter) {}

    public function store(SubscribeNewsletterRequest $request): JsonResponse
    {
        [, $created] = $this->newsletter->subscribe($request->validated('email'), app()->getLocale());

        // Même message dans les deux cas : on ne révèle pas si l'adresse était déjà inscrite.
        return response()->json(['message' => 'Merci, vous êtes inscrit(e) à la newsletter.'], $created ? 201 : 200);
    }
}
