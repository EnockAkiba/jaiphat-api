<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bride;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class BrideAdminService
{
    /** @return Collection<int, Bride> */
    public function all(): Collection
    {
        return Bride::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function save(array $data, ?int $id = null): Bride
    {
        return DB::transaction(function () use ($data, $id): Bride {
            $bride = $id ? Bride::query()->findOrFail($id) : new Bride;

            $bride->fill([
                'name' => $data['name'],
                'gown_name' => $data['gown'] ?? '',
                'image' => MediaUrl::toStored($data['image']),
                'sort_order' => $bride->exists ? $bride->sort_order : ((int) Bride::query()->max('sort_order')) + 1,
            ])->save();

            foreach (['en', 'fr'] as $locale) {
                $bride->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['quote' => $data['translations'][$locale]['quote'] ?? ''],
                );
            }

            return $bride->load('translations');
        });
    }

    public function delete(int $id): void
    {
        Bride::query()->findOrFail($id)->delete();
    }
}