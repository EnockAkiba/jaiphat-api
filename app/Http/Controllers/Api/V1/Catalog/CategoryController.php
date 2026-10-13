<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\CategoryResource;
use App\Services\CatalogService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->catalog->categories());
    }
}
