<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\Concerns\BuildsTranslationPairs;
use App\Models\Product;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class AdminProductResource extends JsonResource
{
    use BuildsTranslationPairs;

    public function toArray(Request $request): array
    {
        $translations = $this->pairs($this->translations, ['excerpt' => 'excerpt', 'description' => 'description']);
        $images = $this->images->sortBy('sort_order')->map(fn ($i) => MediaUrl::from($i->path))->values();
        $category = $this->category?->translations->firstWhere('locale', 'en')?->name ?? $this->category?->slug;
        $color = $this->color?->translations->firstWhere('locale', 'en')?->name ?? $this->color?->slug;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => (float) $this->price,
            'category' => $this->category?->slug,
            'color' => $this->color?->slug,
            'accessory' => (bool) $this->is_accessory,
            'featured' => (bool) $this->is_featured,
            'badge' => $this->badge,
            'images' => $images,
            'image' => $images->first(),
            'sizes' => $this->sizes->sortBy('sort_order')->pluck('label')->values(),
            'categoryName' => $category,
            'colorName' => $color,
            'excerpt' => $translations['en']['excerpt'],
            'description' => $translations['en']['description'],
            'translations' => $translations,
        ];
    }
}
