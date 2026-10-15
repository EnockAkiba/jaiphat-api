<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Color;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ColorAdminService
{
    /** @return Collection<int, Color> */
    public function all(): Collection
    {
        return Color::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function save(array $data, ?int $id = null): Color
    {
        return DB::transaction(function () use ($data, $id): Color {
            $color = $id ? Color::query()->findOrFail($id) : new Color;
            $slug = Str::slug(($data['slug'] ?? '') ?: $data['translations']['en']['name']);

            $taken = Color::query()->where('slug', $slug)->when($id, fn ($q) => $q->where('id', '!=', $id))->exists();
            if ($slug === '' || $taken) {
                throw ValidationException::withMessages(['slug' => 'Ce slug est déjà utilisé.']);
            }

            $color->fill([
                'slug' => $slug,
                'swatch' => strtolower($data['swatch']),
                'image' => MediaUrl::toStored($data['image']),
                'sort_order' => $color->exists ? $color->sort_order : ((int) Color::query()->max('sort_order')) + 1,
            ])->save();

            foreach (['en', 'fr'] as $locale) {
                if (isset($data['translations'][$locale]['name'])) {
                    $color->translations()->updateOrCreate(['locale' => $locale], ['name' => $data['translations'][$locale]['name']]);
                }
            }

            return $color->load('translations');
        });
    }

    public function delete(int $id): void
    {
        $color = Color::query()->findOrFail($id);

        if ($color->products()->exists()) {
            throw ValidationException::withMessages(['color' => 'Cette couleur est utilisée par des produits.']);
        }

        $color->delete();
    }
}
