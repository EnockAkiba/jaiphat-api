<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\DTOs\Auth\AuthResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Réponse d'inscription / connexion : { data: { user, token, token_type } } */
class AuthResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var AuthResult $result */
        $result = $this->resource;

        return [
            'user' => new UserResource($result->user),
            'token' => $result->token,
            'token_type' => 'Bearer',
        ];
    }
}
