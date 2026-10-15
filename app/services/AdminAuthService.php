<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AdminAuthService
{
    /** @return array{user: User, token: string} */
    public function login(string $email, string $password): array
    {
        $user = User::query()->where('email', $email)->first();

        // Même message si l'email n'existe pas, si le mot de passe est faux ou si ce n'est pas un admin.
        if ($user === null || ! $user->is_admin || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects.']);
        }

        return ['user' => $user, 'token' => $user->createToken('admin')->plainTextToken];
    }
}