<?php

declare(strict_types=1);

namespace App\Http\Resources\Showcase;

use App\DTOs\Showcase\HomePage;
use App\Http\Resources\Catalog\ProductResource;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HomePage */
class HomePageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $c = $this->content;

        return [
            'hero' => [
                'kicker' => $c->translated('hero_kicker'),
                'title' => $c->translated('hero_title'),
                'script' => $c->translated('hero_script'),
                'text' => $c->translated('hero_text'),
                'image' => MediaUrl::from($c->hero_image),
                'detail_image' => MediaUrl::from($c->hero_detail_image),
                'primary_to' => $c->hero_primary_to,
                'secondary_to' => $c->hero_secondary_to,
            ],
            'stats' => [
                'brides' => (int) $c->stat_brides,
                'rating' => (float) $c->stat_rating,
                'reviews' => (int) $c->stat_reviews,
            ],
            'salon_image' => MediaUrl::from($c->salon_image),
            'band' => [
                'title' => $c->translated('band_title'),
                'items' => $this->bandItems->map(fn ($i) => [
                    'id' => $i->id,
                    'icon' => $i->icon,
                    'title' => $i->translated('title'),
                    'text' => $i->translated('text'),
                ])->values(),
            ],
            'services' => $this->services->map(fn ($s) => [
                'id' => $s->id,
                'icon' => $s->icon,
                'title' => $s->translated('title'),
                'text' => $s->translated('text'),
            ])->values(),
            'featured_products' => ProductResource::collection($this->featuredProducts),
            'testimonials' => TestimonialResource::collection($this->testimonials),
            'brides' => BrideResource::collection($this->brides),
        ];
    }
}
