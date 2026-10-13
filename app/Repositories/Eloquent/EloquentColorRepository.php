<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Color;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LocalizesQueries;
use Illuminate\Database\Eloquent\Collection;

final class EloquentColorRepository implements ColorRepositoryInterface
{
    use LocalizesQueries;

    public function all(): Collection
    {
        return Color::query()
            ->with(['translations' => $this->localized()])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
