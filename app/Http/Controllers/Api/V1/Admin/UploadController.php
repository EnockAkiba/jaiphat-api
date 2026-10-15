<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UploadImageRequest;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    public function __invoke(UploadImageRequest $request, UploadService $uploads): JsonResponse
    {
        return response()->json(['data' => $uploads->storeImage($request->file('file'))], 201);
    }
}
