<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Concerns;

use Closure;

trait LocalizesQueries
{
    /**
     * Contrainte à passer à with(['translations' => ...]) : on ne charge que la langue courante
     * et la langue de secours, au lieu de toutes les traductions (évite de charger du texte inutile).
     */
    protected function localized(): Closure
    {
        $locales = array_values(array_unique([
            app()->getLocale(),
            config('app.fallback_locale'),
        ]));

        return fn ($query) => $query->whereIn('locale', $locales);
    }
}
