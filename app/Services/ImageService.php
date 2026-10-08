<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Server-side listing image processing built on the GD extension.
 *
 * Responsibilities (per TOLETKOI_MASTER_AGENT_PROMPT section 29):
 * - verify the file is a real image (not just a renamed payload)
 * - re-encode to WebP to strip metadata and cut transfer size
 * - bound the longest edge so uploads stay in the 200-300 KB range
 * - generate a thumbnail for listing cards and dashboards
 *
 * Imagick is intentionally not required; GD ships with the XAMPP stack.
 */
class ImageService
{
    /** Longest edge, in pixels, for the stored full-size image. */
    public const MAX_EDGE = 1600;

    /** Longest edge, in pixels, for the generated thumbnail. */
    public const THUMB_EDGE = 480;

    /** WebP quality for the full-size image. */
    public const QUALITY = 82;

    /** WebP quality for the thumbnail. */
    public const THUMB_QUALITY = 75;

    /** MIME types we are willing to decode. */
    protected const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Process an uploaded image and store it plus its thumbnail.
     *
     * @return array{path: string, thumbnail_path: string, width: int, height: int, size: int}
     */
    public function processListingImage(UploadedFile $file, string $directory): array
    {
        $this->assertUsableUpload($file);

        $source = $this->loadImage($file);
        $source = $this->applyExifOrientation($file, $source);

        [$width, $height] = [$this->width($source), $this->height($source)];

        $resized = $this->resizeToMaxEdge($source, self::MAX_EDGE);
        $full = $this->encodeWebp($resized, self::QUALITY);
        $thumb = $this->encodeWebp($this->resizeToMaxEdge($source, self::THUMB_EDGE), self::THUMB_QUALITY);

        $this->free($source);
        if ($resized !== $source) {
            $this->free($resized);
        }

        $basename = Str::random(24);
        $fullPath = trim($directory, '/') . '/' . $basename . '.webp';
        $thumbPath = trim($directory, '/') . '/thumbs/' . $basename . '.webp';

        $disk = Storage::disk('public');
        $disk->put($fullPath, $full);
        $disk->put($thumbPath, $thumb);

        unset($full, $thumb);

        return [
            'path' => $fullPath,
            'thumbnail_path' => $thumbPath,
            'width' => $width,
            'height' => $height,
            'size' => (int) $disk->size($fullPath),
        ];
    }

    /**
     * Delete a stored image and its thumbnail, ignoring missing files.
     */
    public function deleteListingImage(?string $path, ?string $thumbnailPath = null): void
    {
        $disk = Storage::disk('public');

        foreach ([$path, $thumbnailPath] as $candidate) {
            if ($candidate && $disk->exists($candidate)) {
                $disk->delete($candidate);
            }
        }
    }

    /**
     * Reject anything that is not a genuine image of an allowed type.
     */
    protected function assertUsableUpload(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new RuntimeException('The uploaded image is not a valid file upload.');
        }

        if (!extension_loaded('gd')) {
            throw new RuntimeException('The GD extension is required to process listing images.');
        }

        $mime = (string) $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new RuntimeException("Unsupported image type: {$mime}");
        }

        // getimagesize() reads the real header, so a renamed .exe cannot pass.
        $info = @getimagesize($file->getRealPath());
        if ($info === false) {
            throw new RuntimeException('The uploaded file could not be read as an image.');
        }
    }

    /**
     * Decode the upload into a GD resource.
     */
    protected function loadImage(UploadedFile $file): GdImage
    {
        $real = $file->getRealPath();
        $image = @imagecreatefromstring((string) file_get_contents($real));

        if (!$image instanceof GdImage) {
            throw new RuntimeException('Failed to decode the uploaded image.');
        }

        return $image;
    }

    /**
     * Rotate the image according to its EXIF orientation, when present.
     *
     * JPEGs from phones are frequently stored rotated; without this the
     * listing photo appears sideways after conversion.
     */
    protected function applyExifOrientation(UploadedFile $file, GdImage $image): GdImage
    {
        if ($file->getMimeType() !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = $exif['Orientation'] ?? null;

        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => null,
        };

        if ($angle === null) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated instanceof GdImage) {
            $this->free($image);

            return $rotated;
        }

        return $image;
    }

    /**
     * Scale the image down so its longest edge is at most $maxEdge.
     *
     * Images already within the bound are returned untouched, so we never
     * upscale and degrade quality.
     */
    protected function resizeToMaxEdge(GdImage $image, int $maxEdge): GdImage
    {
        $width = $this->width($image);
        $height = $this->height($image);
        $longest = max($width, $height);

        if ($longest <= $maxEdge) {
            return $image;
        }

        $ratio = $maxEdge / $longest;
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF sources.
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    /**
     * Encode a GD resource as WebP and return the raw bytes.
     */
    protected function encodeWebp(GdImage $image, int $quality): string
    {
        if (!function_exists('imagewebp')) {
            throw new RuntimeException('WebP encoding is not available in this GD build.');
        }

        ob_start();
        $ok = imagewebp($image, null, $quality);
        $bytes = (string) ob_get_clean();

        if (!$ok || $bytes === '') {
            throw new RuntimeException('Failed to encode the image as WebP.');
        }

        return $bytes;
    }

    protected function width(GdImage $image): int
    {
        return imagesx($image);
    }

    protected function height(GdImage $image): int
    {
        return imagesy($image);
    }

    protected function free(GdImage $image): void
    {
        imagedestroy($image);
    }
}
