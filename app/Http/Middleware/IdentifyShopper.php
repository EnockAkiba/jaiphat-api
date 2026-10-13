<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\DTOs\Shopping\Shopper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identifie le client pour le panier et la wishlist :
 *  1. utilisateur connecté (Authorization: Bearer <token Sanctum>) -> panier lié à son compte
 *  2. sinon invité : en-tête  X-Guest-Token  (ex. un UUID généré et mémorisé par le frontend)
 * Le Shopper est ensuite injectable dans les contrôleurs : public function index(Shopper $shopper)
 */
class IdentifyShopper
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        if ($user !== null) {
            $shopper = new Shopper(userId: (int) $user->getAuthIdentifier());
        } else {
            $token = $request->header('X-Guest-Token');

            if (! is_string($token) || ! preg_match('/^[A-Za-z0-9_-]{16,64}$/', $token)) {
                return response()->json([
                    'message' => "En-tête X-Guest-Token manquant ou invalide (16 à 64 caractères : lettres, chiffres, - ou _ ; par exemple un UUID).",
                ], 400);
            }

            $shopper = new Shopper(guestToken: $token);
        }

        // Disponible par injection de dépendance pour la suite de la requête.
        app()->instance(Shopper::class, $shopper);

        return $next($request);
    }
}
