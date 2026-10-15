<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\LoginRequest;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName = 'api',
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        $data = $request->validated();

        return new self(
            email: strtolower((string) $data['email']),
            password: (string) $data['password'],
            deviceName: (string) ($data['device_name'] ?? 'api'),
        );
    }
}
