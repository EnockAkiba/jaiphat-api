<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HomeBandItem;
use App\Models\HomeContent;
use App\Models\HomeService;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\DB;

final class HomeAdminService
{
    private const LOCALES = ['en', 'fr'];

    /** Contenu de l'accueil, au format exact attendu par l'admin React. */
    public function get(): array
    {
        $content = HomeContent::query()->with('translations')->firstOrFail();
        $services = HomeService::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();
        $items = HomeBandItem::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();

        $pair = function ($model, array $columns): array {
            $out = [];
            foreach (self::LOCALES as $locale) {
                $row = $model->translations->firstWhere('locale', $locale);
                foreach ($columns as $key => $column) {
                    $out[$locale][$key] = $row?->{$column} ?? '';
                }
            }

            return $out;
        };

        return [
            'hero' => [
                'image' => MediaUrl::from($content->hero_image),
                'detail' => MediaUrl::from($content->hero_detail_image),
                'primaryTo' => $content->hero_primary_to,
                'secondaryTo' => $content->hero_secondary_to,
                'translations' => $pair($content, [
                    'kicker' => 'hero_kicker', 'title' => 'hero_title',
                    'script' => 'hero_script', 'text' => 'hero_text',
                ]),
            ],
            'services' => $services->map(fn (HomeService $s) => [
                'icon' => $s->icon,
                'translations' => $pair($s, ['title' => 'title', 'text' => 'text']),
            ])->values()->all(),
            'band' => [
                'translations' => $pair($content, ['title' => 'band_title']),
                'items' => $items->map(fn (HomeBandItem $i) => [
                    'icon' => $i->icon,
                    'translations' => $pair($i, ['title' => 'title', 'text' => 'text']),
                ])->values()->all(),
            ],
            'stats' => [
                'brides' => $content->stat_brides,
                'rating' => (float) $content->stat_rating,
                'reviews' => $content->stat_reviews,
            ],
            'salon' => MediaUrl::from($content->salon_image),
            'salonImages' => array_values(array_filter([MediaUrl::from($content->salon_image)])),
        ];
    }

    public function update(array $data): array
    {
        DB::transaction(function () use ($data): void {
            $content = HomeContent::query()->firstOrFail();

            $salon = $data['salon'] ?? ($data['salonImages'][0] ?? null);

            $content->update([
                'hero_image' => MediaUrl::toStored($data['hero']['image']),
                'hero_detail_image' => MediaUrl::toStored($data['hero']['detail']),
                'salon_image' => $salon ? MediaUrl::toStored($salon) : $content->salon_image,
                'hero_primary_to' => $data['hero']['primaryTo'],
                'hero_secondary_to' => $data['hero']['secondaryTo'],
                'stat_brides' => $data['stats']['brides'],
                'stat_rating' => $data['stats']['rating'],
                'stat_reviews' => $data['stats']['reviews'],
            ]);

            foreach (self::LOCALES as $locale) {
                $hero = $data['hero']['translations'][$locale] ?? [];

                $content->translations()->updateOrCreate(['locale' => $locale], [
                    'hero_kicker' => $hero['kicker'] ?? '',
                    'hero_title' => $hero['title'] ?? '',
                    'hero_script' => $hero['script'] ?? '',
                    'hero_text' => $hero['text'] ?? '',
                    'band_title' => $data['band']['translations'][$locale]['title'] ?? '',
                ]);
            }

            // Listes courtes sans identifiant côté admin : on les remplace dans l'ordre reçu.
            HomeService::query()->delete();
            foreach (array_values($data['services']) as $position => $service) {
                $this->createItem(new HomeService, $service, $position);
            }

            HomeBandItem::query()->delete();
            foreach (array_values($data['band']['items']) as $position => $item) {
                $this->createItem(new HomeBandItem, $item, $position);
            }
        });

        return $this->get();
    }

    private function createItem(HomeService|HomeBandItem $model, array $data, int $position): void
    {
        $model->fill(['icon' => $data['icon'], 'sort_order' => $position])->save();

        foreach (self::LOCALES as $locale) {
            $model->translations()->create([
                'locale' => $locale,
                'title' => $data['translations'][$locale]['title'] ?? '',
                'text' => $data['translations'][$locale]['text'] ?? '',
            ]);
        }
    }
}