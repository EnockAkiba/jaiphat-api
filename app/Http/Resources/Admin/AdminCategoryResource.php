<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\Concerns\BuildsTranslationPairs;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
class AdminCategoryResource extends JsonResource
{
    use BuildsTranslationPairs;

    public function toArray(Request $request): array
    {
        $translations = $this->pairs($this->translations, ['name' => 'name']);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'menu' => $this->menu,
            'name' => $translations['en']['name'] ?: $translations['fr']['name'],
            'translations' => $translations,
        ];
    }
}
