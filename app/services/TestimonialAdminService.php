<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Testimonial;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class TestimonialAdminService
{
    /** @return Collection<int, Testimonial> */
    public function all(): Collection
    {
        return Testimonial::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function save(array $data, ?int $id = null): Testimonial
    {
        return DB::transaction(function () use ($data, $id): Testimonial {
            $item = $id ? Testimonial::query()->findOrFail($id) : new Testimonial;

            $item->fill([
                'name' => $data['name'],
                'avatar' => MediaUrl::toStored($data['avatar']),
                'sort_order' => $item->exists ? $item->sort_order : ((int) Testimonial::query()->max('sort_order')) + 1,
            ])->save();

            foreach (['en', 'fr'] as $locale) {
                $item->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['quote' => $data['translations'][$locale]['quote'] ?? ''],
                );
            }

            return $item->load('translations');
        });
    }

    public function delete(int $id): void
    {
        Testimonial::query()->findOrFail($id)->delete();
    }
}