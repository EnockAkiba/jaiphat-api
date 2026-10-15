<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Bride;
use App\Models\HomeBandItem;
use App\Models\HomeContent;
use App\Models\HomeService;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Repositories\Contracts\ContentRepositoryInterface;
use App\Repositories\Eloquent\Concerns\LocalizesQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class EloquentContentRepository implements ContentRepositoryInterface
{
    use LocalizesQueries;

    public function settings(): ?Setting
    {
        return Setting::query()->with(['translations' => $this->localized()])->first();
    }

    public function page(string $slug): ?Page
    {
        return Page::query()
            ->with(['translations' => $this->localized()])
            ->where('slug', $slug)
            ->first();
    }

    public function homeContent(): ?HomeContent
    {
        return HomeContent::query()->with(['translations' => $this->localized()])->first();
    }

    public function homeServices(): Collection
    {
        return HomeService::query()
            ->with(['translations' => $this->localized()])
            ->orderBy('sort_order')->orderBy('id')
            ->get();
    }

    public function homeBandItems(): Collection
    {
        return HomeBandItem::query()
            ->with(['translations' => $this->localized()])
            ->orderBy('sort_order')->orderBy('id')
            ->get();
    }

    public function testimonials(?int $limit = null): Collection
    {
        return Testimonial::query()
            ->with(['translations' => $this->localized()])
            ->orderBy('sort_order')->orderBy('id')
            ->when($limit, fn (Builder $q, int $max) => $q->limit($max))
            ->get();
    }

    public function brides(?int $limit = null): Collection
    {
        return Bride::query()
            ->with(['translations' => $this->localized(), 'product'])
            ->orderBy('sort_order')->orderBy('id')
            ->when($limit, fn (Builder $q, int $max) => $q->limit($max))
            ->get();
    }
}
