<?php

declare(strict_types=1);

namespace App\Http\Resources\Catalog;

use App\Support\MediaUrl;
use Illuminate\Http\Request;

/** Version "fiche produit" : tout ProductResource + description, galerie et tailles. */
class ProductDetailResource extends ProductResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => $this->translated('description'),
            'images' => $this->images->map(fn ($image) => MediaUrl::from($image->path))->values(),
            'sizes' => $this->sizes->pluck('label')->values(),
        ];
    }
}
