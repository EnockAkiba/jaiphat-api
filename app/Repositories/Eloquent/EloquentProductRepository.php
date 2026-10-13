<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Catalog\ProductFilterData;
use App\Enums\ProductSort;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LocalizesQueries;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    use LocalizesQueries;

    public function paginate(ProductFilterData $filter): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->when($filter->category, fn (Builder $q, string $slug) => $q
                ->whereHas('category', fn (Builder $c) => $c->where('slug', $slug)))
            ->when($filter->color, fn (Builder $q, string $slug) => $q
                ->whereHas('color', fn (Builder $c) => $c->where('slug', $slug)))
            ->when($filter->accessory !== null, fn (Builder $q) => $q->where('is_accessory', $filter->accessory))
            ->when($filter->featured !== null, fn (Builder $q) => $q->where('is_featured', $filter->featured))
            ->when($filter->minPrice !== null, fn (Builder $q) => $q->where('price', '>=', $filter->minPrice))
            ->when($filter->maxPrice !== null, fn (Builder $q) => $q->where('price', '<=', $filter->maxPrice))
            ->when($filter->search, fn (Builder $q, string $term) => $this->search($q, $term))
            ->tap(fn (Builder $q) => $this->applySort($q, $filter->sort))
            ->paginate($filter->perPage)
            ->withQueryString();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->baseQuery()->where('slug', $slug)->first();
    }

    public function featured(int $limit): Collection
    {
        return $this->baseQuery()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function related(Product $product, int $limit): Collection
    {
        return $this->baseQuery()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    /** Requête de base : toutes les relations dont l'API a besoin, chargées en une fois (pas de N+1). */
    private function baseQuery(): Builder
    {
        return Product::query()->with([
            'translations' => $this->localized(),
            'category.translations' => $this->localized(),
            'color.translations' => $this->localized(),
            'images',
            'sizes',
        ]);
    }

    private function search(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $w) use ($like): void {
            $w->where('name', 'like', $like)
                ->orWhereHas('translations', fn (Builder $t) => $t
                    ->where('excerpt', 'like', $like)
                    ->orWhere('description', 'like', $like));
        });
    }

    private function applySort(Builder $query, ProductSort $sort): void
    {
        match ($sort) {
            ProductSort::PriceAsc => $query->orderBy('price')->orderBy('id'),
            ProductSort::PriceDesc => $query->orderByDesc('price')->orderBy('id'),
            ProductSort::Newest => $query->latest()->orderByDesc('id'),
            ProductSort::Position => $query->orderBy('sort_order')->orderBy('id'),
        };
    }
}
