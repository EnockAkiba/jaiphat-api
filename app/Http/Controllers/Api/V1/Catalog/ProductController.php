<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\DTOs\Catalog\ProductFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ProductIndexRequest;
use App\Http\Resources\Catalog\ProductDetailResource;
use App\Http\Resources\Catalog\ProductResource;
use App\Services\CatalogService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Contrôleur "fin" : requête validée -> DTO -> service -> resource. Aucune logique métier ici. */
class ProductController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->catalog->paginateProducts(ProductFilterData::fromRequest($request))
        );
    }

    public function featured(): AnonymousResourceCollection
    {
        return ProductResource::collection($this->catalog->featuredProducts());
    }

    public function show(string $slug): ProductDetailResource
    {
        return new ProductDetailResource($this->catalog->productBySlug($slug));
    }

    public function related(string $slug): AnonymousResourceCollection
    {
        return ProductResource::collection($this->catalog->relatedProducts($slug));
    }
}
