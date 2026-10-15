<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Auth\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register(RegisterData::fromRequest($request), $this->guestToken($request));

        return (new AuthResource($result))->response()->setStatusCode(201);
    }

    public function login(LoginRequest $request): AuthResource
    {
        return new AuthResource(
            $this->auth->login(LoginData::fromRequest($request), $this->guestToken($request))
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Déconnecté.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /** Jeton invité (facultatif) : s'il est présent, le panier et la wishlist de l'invité seront fusionnés. */
    private function guestToken(Request $request): ?string
    {
        $token = $request->header('X-Guest-Token');

        return is_string($token) ? $token : null;
    }
}
