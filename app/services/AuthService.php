<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Auth\AuthResult;
use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Inscription, connexion, déconnexion. À chaque inscription / connexion, ce que l'invité avait
 * (panier, wishlist) est transféré vers son compte par GuestDataMigrator.
 */
final class AuthService
{
    public function __construct(private readonly GuestDataMigrator $guestData) {}

    public function register(RegisterData $data, ?string $guestToken = null): AuthResult
    {
        $user = DB::transaction(function () use ($data, $guestToken): User {
            // Le mot de passe est haché automatiquement (cast 'hashed' du modèle User).
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $this->guestData->migrate($guestToken, $user->id);

            return $user;
        });

        return $this->issueToken($user, $data->deviceName);
    }

    /** @throws ValidationException si l'email ou le mot de passe est incorrect */
    public function login(LoginData $data, ?string $guestToken = null): AuthResult
    {
        $user = User::query()->where('email', $data->email)->first();

        // Même message dans les deux cas : on ne révèle pas si l'email existe.
        if ($user === null || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects.']);
        }

        $this->guestData->migrate($guestToken, $user->id);

        return $this->issueToken($user, $data->deviceName);
    }

    /** Révoque uniquement le jeton utilisé pour cette requête (les autres appareils restent connectés). */
    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    private function issueToken(User $user, string $deviceName): AuthResult
    {
        return new AuthResult($user, $user->createToken($deviceName)->plainTextToken);
    }
}
