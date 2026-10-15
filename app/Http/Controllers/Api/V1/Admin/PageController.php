<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePageRequest;
use App\Http\Resources\Admin\AdminPageResource;
use App\Services\PageAdminService;

class PageController extends Controller
{
    public function __construct(private readonly PageAdminService $service) {}

    public function show(string $slug): AdminPageResource
    {
        return new AdminPageResource($this->service->get($slug));
    }

    public function update(SavePageRequest $request, string $slug): AdminPageResource
    {
        return new AdminPageResource($this->service->update($slug, $request->validated()));
    }
}