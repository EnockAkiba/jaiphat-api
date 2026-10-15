<?php

declare(strict_types=1);

namespace App\Http\Resources\Showcase;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Setting */
class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'brand' => $this->brand,
            'phone' => $this->phone,
            'phone_href' => $this->phone_href,
            'email' => $this->email,
            'address' => $this->address,
            'instagram' => $this->instagram,
            'free_shipping' => (float) $this->free_shipping,
            'tagline' => $this->translated('tagline'),
            'shipping_note' => $this->translated('shipping_note'),
            'hours' => $this->translated('hours'),
        ];
    }
}
