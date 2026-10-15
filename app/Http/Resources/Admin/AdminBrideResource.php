<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\Concerns\BuildsTranslationPairs;
use App\Models\Bride;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Bride */
class AdminBrideResource extends JsonResource
{
    use BuildsTranslationPairs;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gown' => $this->gown_name,
            'image' => MediaUrl::from($this->image),
            'translations' => $this->pairs($this->translations, ['quote' => 'quote']),
        ];
    }
}