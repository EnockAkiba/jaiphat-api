<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;

interface ColorRepositoryInterface
{
    /** @return Collection<int, Color> */
    public function all(): Collection;
}
