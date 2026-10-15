<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\MediaUrl;
use Illuminate\Http\UploadedFile;

final class UploadService
{
    /** @return array{path: string, url: string} */
    public function storeImage(UploadedFile $file): array
    {
        // store() génère un nom aléatoire : jamais le nom envoyé par l'utilisateur.
        $path = $file->store('uploads', 'public');

        return ['path' => $path, 'url' => (string) MediaUrl::from($path)];
    }
}
