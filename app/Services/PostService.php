<?php

namespace App\Services;

use App\Models\Leader;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Validation\ValidationException;

class PostService
{
    public function __construct(
        protected PhotoService $photoService,
        protected NotificationService $notificationService,
    ) {}

    /**
     * Create a new post with optional photos and optionally notify all parents.
     *
     * @param  array  $data  Keys: 'content' (required), 'latitude', 'longitude', 'photos' (array of UploadedFile), 'send_notification' (bool)
     * @param  Leader  $author
     * @return Post
     *
     * @throws ValidationException
     */
    public function create(array $data, Leader $author): Post
    {
        $this->validateContent($data['content'] ?? '');

        $post = Post::create([
            'leader_id' => $author->id,
            'content' => $data['content'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
        ]);

        if (!empty($data['photos'])) {
            $this->photoService->upload($data['photos'], $post);
        }

        // Only send notifications if explicitly requested (defaults to true for backward compat)
        $sendNotification = $data['send_notification'] ?? true;
        if ($sendNotification) {
            $this->notificationService->notifyAllParents($post);
        }

        return $post;
    }

    /**
     * Update an existing post's content, coordinates, and photos.
     *
     * @param  Post  $post
     * @param  array  $data  Keys: 'content', 'latitude', 'longitude', 'photos' (new files), 'remove_photos' (array of photo IDs)
     * @return Post
     *
     * @throws ValidationException
     */
    public function update(Post $post, array $data): Post
    {
        $this->validateContent($data['content'] ?? '');

        $post->update([
            'content' => $data['content'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? $post->is_pinned,
        ]);

        // Remove specified photos
        if (!empty($data['remove_photos'])) {
            $photosToRemove = Photo::where('post_id', $post->id)
                ->whereIn('id', $data['remove_photos'])
                ->get();

            foreach ($photosToRemove as $photo) {
                $this->photoService->delete($photo);
            }
        }

        // Add new photos
        if (!empty($data['photos'])) {
            $this->photoService->upload($data['photos'], $post);
        }

        return $post->refresh();
    }

    /**
     * Delete a post and all associated photos from the filesystem.
     *
     * The database cascade handles removing photo and notification_log records.
     */
    public function delete(Post $post): void
    {
        // Remove all photo files from the filesystem
        foreach ($post->photos as $photo) {
            $this->photoService->delete($photo);
        }

        // Delete the post (cascades to photos and notification_logs in DB)
        $post->delete();
    }

    /**
     * Validate that post content is between 1 and 5000 characters.
     *
     * @throws ValidationException
     */
    protected function validateContent(string $content): void
    {
        $trimmed = trim($content);

        if (mb_strlen($trimmed) === 0) {
            throw ValidationException::withMessages([
                'content' => ['Post content is required.'],
            ]);
        }

        if (mb_strlen($trimmed) > 5000) {
            throw ValidationException::withMessages([
                'content' => ['Post content must not exceed 5000 characters.'],
            ]);
        }
    }
}
