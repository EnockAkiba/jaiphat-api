<?php

declare(strict_types=1);

namespace App\DTOs\Orders;

use App\Http\Requests\Orders\PlaceOrderRequest;

final readonly class PlaceOrderData
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $address,
        public string $city,
        public ?string $district,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $notes,
    ) {}

    public static function fromRequest(PlaceOrderRequest $request): self
    {
        $v = $request->validated();

        return new self(
            customerName: $v['customer_name'],
            customerEmail: $v['customer_email'],
            customerPhone: $v['customer_phone'],
            address: $v['address'],
            city: $v['city'],
            district: $v['district'] ?? null,
            latitude: isset($v['latitude']) ? (float) $v['latitude'] : null,
            longitude: isset($v['longitude']) ? (float) $v['longitude'] : null,
            notes: $v['notes'] ?? null,
        );
    }
}
