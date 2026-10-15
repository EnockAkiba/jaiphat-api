<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminContactResource;
use App\Services\InboxAdminService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function __construct(private readonly InboxAdminService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return AdminContactResource::collection($this->service->contacts());
    }

    public function update(Request $request, int $id): AdminContactResource
    {
        $data = $request->validate(['read' => ['required', 'boolean']]);

        return new AdminContactResource($this->service->setContactRead($id, (bool) $data['read']));
    }
}