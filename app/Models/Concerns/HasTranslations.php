<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Pour les modèles qui ont une table "<modèle>_translations" (une ligne par langue).
 *
 * Le modèle doit déclarer une relation hasMany nommée `translations()`.
 *
 *   $product->translated('excerpt');          // langue courante, sinon langue de secours
 *   $product->translated('excerpt', 'fr');    // langue explicite
 */
trait HasTranslations
{
    public function translation(?string $locale = null): ?Model
    {
        $locale ??= app()->getLocale();

        // $this->translations : utilise la relation déjà chargée (eager loading) ou la charge.
        $translations = $this->translations;

        return $translations->firstWhere('locale', $locale)
            ?? $translations->firstWhere('locale', config('app.fallback_locale'));
    }

    public function translated(string $attribute, ?string $locale = null): mixed
    {
        return $this->translation($locale)?->getAttribute($attribute);
    }
}
