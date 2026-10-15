<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Showcase\HomePage;
use App\Models\HomeContent;
use App\Repositories\Contracts\ContentRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Assemble la page d'accueil : contenu éditorial + produits vedettes du catalogue.
 * Le frontend récupère tout en UN seul appel (GET /home) au lieu de six.
 */
final class HomePageService
{
    public const FEATURED_PRODUCTS = 4;

    public const TESTIMONIALS = 6;

    public const BRIDES = 8;

    public function __construct(
        private readonly ContentRepositoryInterface $content,
        private readonly ProductRepositoryInterface $products,
    ) {}

    /** @throws ModelNotFoundException si le contenu d'accueil n'a pas encore été créé */
    public function home(): HomePage
    {
        $content = $this->content->homeContent()
            ?? throw (new ModelNotFoundException)->setModel(HomeContent::class);

        return new HomePage(
            content: $content,
            services: $this->content->homeServices(),
            bandItems: $this->content->homeBandItems(),
            featuredProducts: $this->products->featured(self::FEATURED_PRODUCTS),
            testimonials: $this->content->testimonials(self::TESTIMONIALS),
            brides: $this->content->brides(self::BRIDES),
        );
    }
}
