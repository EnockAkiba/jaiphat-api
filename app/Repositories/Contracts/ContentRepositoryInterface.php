<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\HomeContent;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

/** Lecture du contenu de la vitrine (réglages, accueil, témoignages, mariées, pages). */
interface ContentRepositoryInterface
{
    public function settings(): ?Setting;

    public function page(string $slug): ?Page;

    public function homeContent(): ?HomeContent;

    /** @return Collection<int, \App\Models\HomeService> */
    public function homeServices(): Collection;

    /** @return Collection<int, \App\Models\HomeBandItem> */
    public function homeBandItems(): Collection;

    /** @return Collection<int, \App\Models\Testimonial> */
    public function testimonials(?int $limit = null): Collection;

    /** @return Collection<int, \App\Models\Bride> */
    public function brides(?int $limit = null): Collection;
}
