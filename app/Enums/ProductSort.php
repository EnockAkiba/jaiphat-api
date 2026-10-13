<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductSort: string
{
    case Position = 'position';
    case Newest = 'newest';
    case PriceAsc = 'price_asc';
    case PriceDesc = 'price_desc';
}
