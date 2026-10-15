<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\RegisterRequest;

final readonly class RegisterData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $deviceName = 'api',
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: (string) $data['name'],
            email: strtolower((string) $data['email']),
            password: (string) $data['password'],
            deviceName: (string) ($data['device_name'] ?? 'api'),
        );
    }
}
