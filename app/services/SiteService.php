<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Models\Setting;
use App\Repositories\Contracts\ContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/** Contenu public du site, hors page d'accueil : réglages, pages, témoignages, mariées. */
final class SiteService
{
    public function __construct(private readonly ContentRepositoryInterface $content) {}

    /** @throws ModelNotFoundException */
    public function settings(): Setting
    {
        return $this->content->settings()
            ?? throw (new ModelNotFoundException)->setModel(Setting::class);
    }

    /** @throws ModelNotFoundException */
    public function page(string $slug): Page
    {
        return $this->content->page($slug)
            ?? throw (new ModelNotFoundException)->setModel(Page::class, [$slug]);
    }

    /** @return Collection<int, \App\Models\Testimonial> */
    public function testimonials(): Collection
    {
        return $this->content->testimonials();
    }

    /** @return Collection<int, \App\Models\Bride> */
    public function brides(): Collection
    {
        return $this->content->brides();
    }
}
