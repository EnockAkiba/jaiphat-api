<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Setting */
class AdminSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translations = [];
        foreach (['en', 'fr'] as $locale) {
            $t = $this->translations->firstWhere('locale', $locale);
            $translations[$locale] = [
                'tagline' => $t?->tagline ?? '',
                'shippingNote' => $t?->shipping_note ?? '',
                'hours' => $t?->hours ?? '',
            ];
        }

        return [
            'brand' => $this->brand,
            'phone' => $this->phone,
            'phoneHref' => $this->phone_href,
            'email' => $this->email,
            'address' => $this->address,
            'freeShipping' => (float) $this->free_shipping,
            'instagram' => $this->instagram,
            'translations' => $translations,
        ];
    }
}