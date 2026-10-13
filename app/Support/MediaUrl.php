<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Transforme le chemin stocké en base en URL publique.
 * - déjà une URL complète  -> renvoyée telle quelle
 * - sinon                  -> URL du disque "public" (storage/app/public, après `php artisan storage:link`)
 */
final class MediaUrl
{
    public static function from(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
