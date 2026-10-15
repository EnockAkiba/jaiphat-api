<?php

declare(strict_types=1);

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/** Toute photo envoyée passe ici : redressée, redimensionnée si besoin, convertie en WebP. */
final class ImageOptimizer
{
    private const MAX_EDGE = 2000; // px, plus grand côté
    private const QUALITY = 85;    // WebP : 80-90 = visuellement sans perte

    /** @return string chemin relatif sur le disque public, ex. "uploads/xxxx.webp" */
    public function store(UploadedFile $file, string $folder = 'uploads'): string
    {
        if (! function_exists('imagewebp')) {
            throw new RuntimeException('Le support WebP de GD est absent (extension php-gd).');
        }

        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($source === false) {
            throw ValidationException::withMessages(['file' => 'Image illisible ou corrompue.']);
        }

        $image = $this->shrink($this->orient($source, $file));

        $relative = $folder.'/'.Str::uuid().'.webp';
        $absolute = Storage::disk('public')->path($relative);
        File::ensureDirectoryExists(dirname($absolute));

        if (! imagewebp($image, $absolute, self::QUALITY)) {
            throw new RuntimeException('Échec de la conversion WebP.');
        }

        return $relative;
    }

    /** Applique l'orientation EXIF (photos prises au téléphone). */
    private function orient(GdImage $image, UploadedFile $file): GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath());

        $angle = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        return imagerotate($image, $angle, 0) ?: $image;
    }

    /** Réduit à MAX_EDGE si nécessaire, sans jamais agrandir ; garde la transparence. */
    private function shrink(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::MAX_EDGE / max($width, $height));

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }
}