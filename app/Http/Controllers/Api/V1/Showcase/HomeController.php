<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Showcase;

use App\Http\Controllers\Controller;
use App\Http\Resources\Showcase\HomePageResource;
use App\Services\HomePageService;

class HomeController extends Controller
{
    public function __construct(private readonly HomePageService $home) {}

    public function show(): HomePageResource
    {
        return new HomePageResource($this->home->home());
    }
}
