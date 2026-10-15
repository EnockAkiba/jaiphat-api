<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Chemins d'images : ce qu'on STOCKE en base et ce qu'on AFFICHE.
 *  - fichier envoyé par l'admin -> stocké "uploads/xxx.jpg" (disque public), affiché "/storage/uploads/xxx.jpg"
 *  - image du front ("/images/...") ou URL complète -> stockée et affichée telle quelle
 */
final class MediaUrl
{
    /** Chemin stocké -> URL publique. */
    public static function from(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }

    /** URL reçue du front -> chemin à stocker (retire le préfixe /storage/ ajouté à l'affichage). */
    public static function toStored(string $value): string
    {
        $value = trim($value);
        $base = Storage::disk('public')->url('');

        foreach ([$base, '/storage/'] as $prefix) {
            if ($prefix !== '' && Str::startsWith($value, $prefix)) {
                return ltrim(Str::after($value, $prefix), '/');
            }
        }

        return $value;
    }
}
