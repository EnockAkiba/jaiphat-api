<?php

declare(strict_types=1);

namespace App\DTOs\Forms;

use App\Http\Requests\Forms\BookAppointmentRequest;
use Carbon\CarbonImmutable;

final readonly class BookAppointmentData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public CarbonImmutable $date,
        public string $time,
        public string $party,
        public ?string $notes,
        public ?int $userId = null,
    ) {}

    public static function fromRequest(BookAppointmentRequest $request): self
    {
        $v = $request->validated();

        return new self(
            name: $v['name'],
            email: $v['email'],
            phone: $v['phone'] ?? null,
            date: CarbonImmutable::createFromFormat('Y-m-d', $v['date'])->startOfDay(),
            time: $v['time'],
            party: $v['party'],
            notes: $v['notes'] ?? null,
            userId: $request->user('sanctum')?->id,
        );
    }
}
