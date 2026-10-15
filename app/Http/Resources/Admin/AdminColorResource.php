<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\Concerns\BuildsTranslationPairs;
use App\Models\Color;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Color */
class AdminColorResource extends JsonResource
{
    use BuildsTranslationPairs;

    public function toArray(Request $request): array
    {
        $translations = $this->pairs($this->translations, ['name' => 'name']);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $translations['en']['name'] ?: $translations['fr']['name'],
            'swatch' => $this->swatch,
            'image' => MediaUrl::from($this->image),
            'translations' => $translations,
        ];
    }
}
