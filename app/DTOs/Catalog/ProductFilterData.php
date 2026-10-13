<?php

declare(strict_types=1);

namespace App\DTOs\Catalog;

use App\Enums\ProductSort;
use App\Http\Requests\Catalog\ProductIndexRequest;

/**
 * Filtres du catalogue, typés. Le service et le repository ne voient jamais l'objet Request :
 * ils ne dépendent que de ce DTO, donc ils restent réutilisables (commande artisan, job, test...).
 */
final readonly class ProductFilterData
{
    public function __construct(
        public ?string $category = null,
        public ?string $color = null,
        public ?bool $accessory = null,
        public ?bool $featured = null,
        public ?string $search = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ProductSort $sort = ProductSort::Position,
        public int $perPage = 12,
    ) {}

    public static function fromRequest(ProductIndexRequest $request): self
    {
        $data = $request->validated();

        return new self(
            category: $data['category'] ?? null,
            color: $data['color'] ?? null,
            accessory: isset($data['accessory']) ? $request->boolean('accessory') : null,
            featured: isset($data['featured']) ? $request->boolean('featured') : null,
            search: $data['q'] ?? null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            sort: ProductSort::tryFrom($data['sort'] ?? '') ?? ProductSort::Position,
            perPage: (int) ($data['per_page'] ?? 12),
        );
    }
}
