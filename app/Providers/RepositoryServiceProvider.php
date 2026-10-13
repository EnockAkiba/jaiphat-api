<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Repositories\Eloquent\EloquentColorRepository;
use App\Repositories\Eloquent\EloquentProductRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Seul endroit qui sait quelle implémentation correspond à quelle interface.
 * Pour changer de source de données (cache, API externe, faux repository de test),
 * on ne modifie que cette liste.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        CategoryRepositoryInterface::class => EloquentCategoryRepository::class,
        ColorRepositoryInterface::class => EloquentColorRepository::class,
    ];
}
