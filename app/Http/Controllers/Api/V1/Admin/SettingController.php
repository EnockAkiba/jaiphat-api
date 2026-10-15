<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Http\Resources\Admin\AdminSettingResource;
use App\Services\SettingAdminService;

class SettingController extends Controller
{
    public function __construct(private readonly SettingAdminService $settings) {}

    public function show(): AdminSettingResource
    {
        return new AdminSettingResource($this->settings->get());
    }

    public function update(UpdateSettingsRequest $request): AdminSettingResource
    {
        return new AdminSettingResource($this->settings->update($request->validated()));
    }
}