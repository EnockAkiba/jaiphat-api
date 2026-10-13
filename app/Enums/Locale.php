<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Langues supportées par l'API.
 * Pour ajouter une langue : ajoute un case ici, puis insère les lignes *_translations.
 */
enum Locale: string
{
    case Fr = 'fr';
    case En = 'en';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
