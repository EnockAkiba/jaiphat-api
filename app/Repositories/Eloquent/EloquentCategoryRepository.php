<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LocalizesQueries;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    use LocalizesQueries;

    public function all(): Collection
    {
        return Category::query()
            ->with(['translations' => $this->localized()])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
