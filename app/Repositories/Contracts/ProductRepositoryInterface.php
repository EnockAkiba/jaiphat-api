<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Catalog\ProductFilterData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function paginate(ProductFilterData $filter): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    /** @return Collection<int, Product> */
    public function featured(int $limit): Collection;

    /** @return Collection<int, Product> */
    public function related(Product $product, int $limit): Collection;
}
