<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Concerns;

use Illuminate\Support\Collection;

trait BuildsTranslationPairs
{
    /**
     * Rassemble les lignes de traduction en { en: {...}, fr: {...} } (toujours les deux langues).
     *
     * @param  Collection<int, \Illuminate\Database\Eloquent\Model>  $rows
     * @param  array<string, string>  $columns  clé envoyée au front => colonne en base
     * @return array<string, array<string, mixed>>
     */
    protected function pairs(Collection $rows, array $columns): array
    {
        $out = [];
        foreach (['en', 'fr'] as $locale) {
            $row = $rows->firstWhere('locale', $locale);
            foreach ($columns as $key => $column) {
                $out[$locale][$key] = $row?->{$column} ?? '';
            }
        }

        return $out;
    }
}
