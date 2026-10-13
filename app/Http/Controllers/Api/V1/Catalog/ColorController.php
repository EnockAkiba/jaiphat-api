<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ColorResource;
use App\Services\CatalogService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ColorController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(): AnonymousResourceCollection
    {
        return ColorResource::collection($this->catalog->colors());
    }
}
