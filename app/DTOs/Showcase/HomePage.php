<?php

declare(strict_types=1);

namespace App\DTOs\Showcase;

use App\Models\Bride;
use App\Models\HomeBandItem;
use App\Models\HomeContent;
use App\Models\HomeService;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Collection;

/** Tout ce qu'il faut pour afficher la page d'accueil, rassemblé en un seul objet. */
final readonly class HomePage
{
    /**
     * @param  Collection<int, HomeService>  $services
     * @param  Collection<int, HomeBandItem>  $bandItems
     * @param  Collection<int, Product>  $featuredProducts
     * @param  Collection<int, Testimonial>  $testimonials
     * @param  Collection<int, Bride>  $brides
     */
    public function __construct(
        public HomeContent $content,
        public Collection $services,
        public Collection $bandItems,
        public Collection $featuredProducts,
        public Collection $testimonials,
        public Collection $brides,
    ) {}
}
