<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Showcase;

use App\Http\Controllers\Controller;
use App\Http\Resources\Showcase\BrideResource;
use App\Http\Resources\Showcase\PageResource;
use App\Http\Resources\Showcase\SettingResource;
use App\Http\Resources\Showcase\TestimonialResource;
use App\Services\SiteService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SiteController extends Controller
{
    public function __construct(private readonly SiteService $site) {}

    public function settings(): SettingResource
    {
        return new SettingResource($this->site->settings());
    }

    public function page(string $slug): PageResource
    {
        return new PageResource($this->site->page($slug));
    }

    public function testimonials(): AnonymousResourceCollection
    {
        return TestimonialResource::collection($this->site->testimonials());
    }

    public function brides(): AnonymousResourceCollection
    {
        return BrideResource::collection($this->site->brides());
    }
}
