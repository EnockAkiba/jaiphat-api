<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ProductAdminService
{
    private const GOWN_SIZES = ['0', '2', '4', '6', '8', '10', '12', '14', '16'];

    private const RELATIONS = ['category.translations', 'color.translations', 'translations', 'images', 'sizes'];

    /** @return Collection<int, Product> */
    public function all(): Collection
    {
        return Product::query()->with(self::RELATIONS)->orderBy('sort_order')->orderBy('id')->get();
    }

    public function save(array $data, ?int $id = null): Product
    {
        return DB::transaction(function () use ($data, $id): Product {
            $product = $id ? Product::query()->findOrFail($id) : new Product;
            $slug = Str::slug(($data['slug'] ?? '') ?: $data['name']);

            $taken = Product::query()->where('slug', $slug)->when($id, fn ($q) => $q->where('id', '!=', $id))->exists();
            if ($slug === '' || $taken) {
                throw ValidationException::withMessages(['slug' => 'Ce slug est déjà utilisé.']);
            }

            $accessory = (bool) ($data['accessory'] ?? false);

            $product->fill([
                'slug' => $slug,
                'name' => trim($data['name']),
                'price' => $data['price'],
                'category_id' => Category::query()->where('slug', $data['category'])->value('id'),
                'color_id' => Color::query()->where('slug', $data['color'])->value('id'),
                'is_accessory' => $accessory,
                'is_featured' => (bool) ($data['featured'] ?? false),
                'badge' => filled($data['badge'] ?? null) ? trim($data['badge']) : null,
                'sort_order' => $product->exists ? $product->sort_order : ((int) Product::query()->max('sort_order')) + 1,
            ])->save();

            foreach (['en', 'fr'] as $locale) {
                if (isset($data['translations'][$locale])) {
                    $product->translations()->updateOrCreate(['locale' => $locale], [
                        'excerpt' => $data['translations'][$locale]['excerpt'] ?? '',
                        'description' => $data['translations'][$locale]['description'] ?? '',
                    ]);
                }
            }

            // Photos et tailles : on remplace l'ensemble, dans l'ordre reçu (la première photo est la couverture).
            $product->images()->delete();
            foreach (array_values($data['images']) as $i => $path) {
                $product->images()->create(['path' => MediaUrl::toStored($path), 'sort_order' => $i]);
            }

            $sizes = collect($data['sizes'] ?? [])->map(fn ($s) => trim((string) $s))->filter()->unique()->values();
            if ($sizes->isEmpty()) {
                $sizes = collect($accessory ? ['OS'] : self::GOWN_SIZES);
            }
            $product->sizes()->delete();
            foreach ($sizes as $i => $label) {
                $product->sizes()->create(['label' => $label, 'sort_order' => $i]);
            }

            return $product->load(self::RELATIONS);
        });
    }

    public function delete(int $id): void
    {
        // Les lignes de panier / wishlist / traductions / photos partent en cascade ; les commandes gardent leur instantané.
        Product::query()->findOrFail($id)->delete();
    }
}
