<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ImageUploadService
{
    public function store(UploadedFile $file, string $directory, int $maxWidth): string
    {
        $directory = trim($directory, '/');

        if ($this->canEncodeWebp()) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                $image->scaleDown(width: $maxWidth);

                $path = $directory . '/' . Str::random(40) . '.webp';
                Storage::disk('public')->put($path, (string) $image->toWebp(80));

                return $path;
            } catch (Throwable $exception) {
                Log::warning('Image optimization failed; storing the original upload.', [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)
            ? $extension
            : 'bin';
        $path = $directory . '/' . Str::random(40) . '.' . $extension;

        Storage::disk('public')->putFileAs($directory, $file, basename($path));

        return $path;
    }

    private function canEncodeWebp(): bool
    {
        return extension_loaded('gd') && function_exists('imagewebp');
    }
}
