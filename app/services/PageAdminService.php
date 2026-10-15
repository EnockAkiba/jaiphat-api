<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\DB;

final class PageAdminService
{
    public function get(string $slug): Page
    {
        // 404 si la page n'existe pas : les pages sont créées par le seeder, pas par l'admin.
        return Page::query()->with('translations')->where('slug', $slug)->firstOrFail();
    }

    public function update(string $slug, array $data): Page
    {
        return DB::transaction(function () use ($slug, $data): Page {
            $page = Page::query()->where('slug', $slug)->firstOrFail();

            $page->update([
                'image' => ($data['image'] ?? '') !== '' ? MediaUrl::toStored($data['image']) : null,
                'secondary_image' => ($data['secondary'] ?? '') !== '' ? MediaUrl::toStored($data['secondary']) : null,
            ]);

            foreach (['en', 'fr'] as $locale) {
                $t = $data['translations'][$locale] ?? [];

                $page->translations()->updateOrCreate(['locale' => $locale], [
                    'kicker' => $t['kicker'] ?? '',
                    'title' => $t['title'] ?? '',
                    'lede' => $t['lede'] ?? '',
                    'paragraphs' => array_values($t['paragraphs'] ?? []),
                    'values' => array_values($t['values'] ?? []),
                ]);
            }

            return $page->load('translations');
        });
    }
}