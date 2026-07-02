<?php

namespace App\Services;

use App\Models\Photo;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PhotoService
{
    /**
     * Maximum file size in kilobytes (10 MB).
     */
    protected const MAX_FILE_SIZE_KB = 10240;

    /**
     * Accepted MIME types for photo uploads.
     */
    protected const ACCEPTED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Upload an array of files and associate them with a post.
     *
     * @param  array<UploadedFile>  $files
     * @param  Post  $post
     * @return array<Photo>
     *
     * @throws ValidationException
     */
    public function upload(array $files, Post $post): array
    {
        $this->validateFiles($files);

        $currentMaxOrder = $post->photos()->max('sort_order');
        $sortOrder = $currentMaxOrder !== null ? $currentMaxOrder + 1 : 0;

        $photos = [];

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension() ?: $this->extensionFromMime($file->getMimeType());
            $filename = Str::uuid() . '.' . $extension;

            Storage::disk('photos')->put($filename, file_get_contents($file->getRealPath()));

            $photo = Photo::create([
                'post_id' => $post->id,
                'filename' => $filename,
                'path' => $filename,
                'sort_order' => $sortOrder,
            ]);

            $photos[] = $photo;
            $sortOrder++;
        }

        return $photos;
    }

    /**
     * Delete a photo from the filesystem and database.
     */
    public function delete(Photo $photo): void
    {
        Storage::disk('photos')->delete($photo->path);

        $photo->delete();
    }

    /**
     * Validate all files meet size and type requirements.
     *
     * @param  array<UploadedFile>  $files
     *
     * @throws ValidationException
     */
    protected function validateFiles(array $files): void
    {
        foreach ($files as $index => $file) {
            if (!$file instanceof UploadedFile) {
                throw ValidationException::withMessages([
                    "photos.{$index}" => ['Each item must be a valid uploaded file.'],
                ]);
            }

            $fileSizeKb = $file->getSize() / 1024;
            if ($fileSizeKb > self::MAX_FILE_SIZE_KB) {
                throw ValidationException::withMessages([
                    "photos.{$index}" => ['Each photo must not exceed 10 MB.'],
                ]);
            }

            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, self::ACCEPTED_MIME_TYPES, true)) {
                throw ValidationException::withMessages([
                    "photos.{$index}" => ['Each photo must be a JPEG, PNG, or WebP file.'],
                ]);
            }
        }
    }

    /**
     * Determine a file extension from MIME type.
     */
    protected function extensionFromMime(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
