<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Détermine la langue de la requête : ?lang=fr en priorité, sinon l'en-tête Accept-Language,
 * sinon APP_LOCALE. Seules les langues de l'enum Locale sont acceptées.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $candidate = $request->query('lang');

        if (! is_string($candidate) && $request->headers->has('Accept-Language')) {
            $candidate = $request->getPreferredLanguage(Locale::values());
        }

        $locale = Locale::tryFrom(strtolower(substr((string) $candidate, 0, 2)))?->value
            ?? config('app.locale');

        app()->setLocale($locale);

        return $next($request);
    }
}
