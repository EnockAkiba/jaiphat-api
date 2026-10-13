<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Catalog\ProductFilterData;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Cas d'usage du catalogue. C'est ici que vivra la logique métier (règles de visibilité,
 * cache, prix promotionnels...). Le contrôleur n'appelle QUE ce service.
 */
final class CatalogService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CategoryRepositoryInterface $categories,
        private readonly ColorRepositoryInterface $colors,
    ) {}

    public function paginateProducts(ProductFilterData $filter): LengthAwarePaginator
    {
        return $this->products->paginate($filter);
    }

    /** @throws ModelNotFoundException */
    public function productBySlug(string $slug): Product
    {
        return $this->products->findBySlug($slug)
            ?? throw (new ModelNotFoundException)->setModel(Product::class, [$slug]);
    }

    /** @return Collection<int, Product> */
    public function featuredProducts(int $limit = 8): Collection
    {
        return $this->products->featured($limit);
    }

    /**
     * @return Collection<int, Product>
     *
     * @throws ModelNotFoundException
     */
    public function relatedProducts(string $slug, int $limit = 4): Collection
    {
        return $this->products->related($this->productBySlug($slug), $limit);
    }

    /** @return Collection<int, Category> */
    public function categories(): Collection
    {
        return $this->categories->all();
    }

    /** @return Collection<int, Color> */
    public function colors(): Collection
    {
        return $this->colors->all();
    }
}
