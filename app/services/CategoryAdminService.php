<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CategoryAdminService
{
    /** @return Collection<int, Category> */
    public function all(): Collection
    {
        return Category::query()->with('translations')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function save(array $data, ?int $id = null): Category
    {
        return DB::transaction(function () use ($data, $id): Category {
            $category = $id ? Category::query()->findOrFail($id) : new Category;
            $slug = Str::slug(($data['slug'] ?? '') ?: $data['translations']['en']['name']);

            $taken = Category::query()->where('slug', $slug)->when($id, fn ($q) => $q->where('id', '!=', $id))->exists();
            if ($slug === '' || $taken) {
                throw ValidationException::withMessages(['slug' => 'Ce slug est déjà utilisé.']);
            }

            $category->fill([
                'slug' => $slug,
                'menu' => $data['menu'] ?? 'collections',
                'sort_order' => $category->exists ? $category->sort_order : ((int) Category::query()->max('sort_order')) + 1,
            ])->save();

            foreach (['en', 'fr'] as $locale) {
                if (isset($data['translations'][$locale]['name'])) {
                    $category->translations()->updateOrCreate(['locale' => $locale], ['name' => $data['translations'][$locale]['name']]);
                }
            }

            return $category->load('translations');
        });
    }

    public function delete(int $id): void
    {
        $category = Category::query()->findOrFail($id);

        if ($category->products()->exists()) {
            throw ValidationException::withMessages(['category' => 'Cette catégorie contient encore des produits.']);
        }

        $category->delete();
    }
}
