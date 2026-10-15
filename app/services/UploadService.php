<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\MediaUrl;
use Illuminate\Http\UploadedFile;

final class UploadService
{
    /** @return array{path: string, url: string} */

      public function __construct(private readonly ImageOptimizer $optimizer) {}

    /** @return array{path: string, url: string} */
    public function storeImage(UploadedFile $file): array
    {
        $path = $this->optimizer->store($file);

        return ['path' => $path, 'url' => (string) MediaUrl::from($path)];
    }

}
