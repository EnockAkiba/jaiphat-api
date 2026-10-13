<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use App\Repositories\Eloquent\EloquentCartRepository;
use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Repositories\Eloquent\EloquentColorRepository;
use App\Repositories\Eloquent\EloquentProductRepository;
use App\Repositories\Eloquent\EloquentWishlistRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Seul endroit qui sait quelle implémentation correspond à quelle interface.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        // Catalogue
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        CategoryRepositoryInterface::class => EloquentCategoryRepository::class,
        ColorRepositoryInterface::class => EloquentColorRepository::class,
        // Panier & wishlist
        CartRepositoryInterface::class => EloquentCartRepository::class,
        WishlistRepositoryInterface::class => EloquentWishlistRepository::class,
    ];
}
