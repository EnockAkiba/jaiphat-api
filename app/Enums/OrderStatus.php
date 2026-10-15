<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';      // reçue, frais de livraison à confirmer
    case Confirmed = 'confirmed';  // frais communiqués
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
