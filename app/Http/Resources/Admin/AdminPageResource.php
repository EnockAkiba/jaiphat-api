<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Page;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Page */
class AdminPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translations = [];
        foreach (['en', 'fr'] as $locale) {
            $t = $this->translations->firstWhere('locale', $locale);
            $translations[$locale] = [
                'kicker' => $t?->kicker ?? '',
                'title' => $t?->title ?? '',
                'lede' => $t?->lede ?? '',
                'paragraphs' => $t?->paragraphs ?? [],
                'values' => $t?->values ?? [],
            ];
        }

        return [
            'slug' => $this->slug,
            'image' => MediaUrl::from($this->image) ?? '',
            'secondary' => MediaUrl::from($this->secondary_image) ?? '',
            'translations' => $translations,
        ];
    }
}