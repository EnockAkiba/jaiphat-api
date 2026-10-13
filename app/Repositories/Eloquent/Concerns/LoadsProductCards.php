<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Concerns;

/**
 * Relations à charger pour afficher un produit sous forme de "carte" (ProductResource),
 * à partir d'une relation parente : 'items.product' (panier) ou 'products' (wishlist).
 */
trait LoadsProductCards
{
    use LocalizesQueries;

    /** @return array<string, \Closure|string> */
    protected function productCardRelations(string $path): array
    {
        return [
            "{$path}.translations" => $this->localized(),
            "{$path}.category.translations" => $this->localized(),
            "{$path}.color.translations" => $this->localized(),
            "{$path}.images",
        ];
    }
}
