<?php

namespace Tests\Unit\Services;

use App\Models\Leader;
use App\Models\Photo;
use App\Models\Post;
use App\Services\NotificationResult;
use App\Services\NotificationService;
use App\Services\PhotoService;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostService $postService;
    private PhotoService $photoService;
    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('photos');

        $this->photoService = $this->app->make(PhotoService::class);
        $this->notificationService = $this->mock(NotificationService::class);
        $this->notificationService
            ->shouldReceive('notifyAllParents')
            ->andReturn(new NotificationResult(sent: 0, failed: 0));

        $this->postService = new PostService(
            $this->photoService,
            $this->notificationService,
        );
    }

    public function test_create_stores_post_with_content(): void
    {
        $leader = Leader::factory()->create();

        $post = $this->postService->create([
            'content' => 'Hello from the expedition!',
        ], $leader);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'leader_id' => $leader->id,
            'content' => 'Hello from the expedition!',
        ]);
    }

    public function test_create_stores_post_with_coordinates(): void
    {
        $leader = Leader::factory()->create();

        $post = $this->postService->create([
            'content' => 'We are here!',
            'latitude' => 51.5074000,
            'longitude' => -0.1278000,
        ], $leader);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'latitude' => 51.5074000,
            'longitude' => -0.1278000,
        ]);
    }

    public function test_create_stores_post_without_coordinates_when_not_provided(): void
    {
        $leader = Leader::factory()->create();

        $post = $this->postService->create([
            'content' => 'No location info.',
        ], $leader);

        $this->assertNull($post->latitude);
        $this->assertNull($post->longitude);
    }

    public function test_create_uploads_photos_when_provided(): void
    {
        $leader = Leader::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $post = $this->postService->create([
            'content' => 'Check out this photo!',
            'photos' => [$file],
        ], $leader);

        $this->assertCount(1, $post->photos);
        Storage::disk('photos')->assertExists($post->photos->first()->path);
    }

    public function test_create_calls_notification_service(): void
    {
        $leader = Leader::factory()->create();

        // Recreate mock with once() expectation specifically
        $notificationMock = \Mockery::mock(NotificationService::class);
        $notificationMock
            ->shouldReceive('notifyAllParents')
            ->once()
            ->andReturn(new NotificationResult(sent: 1, failed: 0));

        $service = new PostService($this->photoService, $notificationMock);

        $service->create([
            'content' => 'New update!',
        ], $leader);
    }

    public function test_create_rejects_empty_content(): void
    {
        $leader = Leader::factory()->create();

        $this->expectException(ValidationException::class);

        $this->postService->create([
            'content' => '',
        ], $leader);
    }

    public function test_create_rejects_whitespace_only_content(): void
    {
        $leader = Leader::factory()->create();

        $this->expectException(ValidationException::class);

        $this->postService->create([
            'content' => '   ',
        ], $leader);
    }

    public function test_create_rejects_content_exceeding_5000_characters(): void
    {
        $leader = Leader::factory()->create();

        $this->expectException(ValidationException::class);

        $this->postService->create([
            'content' => str_repeat('a', 5001),
        ], $leader);
    }

    public function test_create_accepts_content_at_5000_characters(): void
    {
        $leader = Leader::factory()->create();

        $post = $this->postService->create([
            'content' => str_repeat('a', 5000),
        ], $leader);

        $this->assertEquals(5000, mb_strlen($post->content));
    }

    public function test_update_changes_content(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'Original content',
        ]);

        $updated = $this->postService->update($post, [
            'content' => 'Updated content',
        ]);

        $this->assertEquals('Updated content', $updated->content);
    }

    public function test_update_changes_coordinates(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'Some content',
            'latitude' => 51.0000000,
            'longitude' => -0.1000000,
        ]);

        $updated = $this->postService->update($post, [
            'content' => 'Some content',
            'latitude' => 52.0000000,
            'longitude' => -1.0000000,
        ]);

        $this->assertEquals(52.0000000, $updated->latitude);
        $this->assertEquals(-1.0000000, $updated->longitude);
    }

    public function test_update_preserves_created_at(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'Original',
        ]);

        $originalCreatedAt = $post->created_at;

        // Small delay to ensure updated_at would differ
        $this->travel(5)->minutes();

        $updated = $this->postService->update($post, [
            'content' => 'Modified',
        ]);

        $this->assertEquals($originalCreatedAt->toDateTimeString(), $updated->created_at->toDateTimeString());
    }

    public function test_update_removes_specified_photos(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'Content with photos',
        ]);

        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
        $photos = $this->photoService->upload([$file], $post);
        $photoId = $photos[0]->id;
        $photoPath = $photos[0]->path;

        $this->postService->update($post, [
            'content' => 'Content with photos',
            'remove_photos' => [$photoId],
        ]);

        $this->assertDatabaseMissing('photos', ['id' => $photoId]);
        Storage::disk('photos')->assertMissing($photoPath);
    }

    public function test_update_adds_new_photos(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'Content',
        ]);

        $file = UploadedFile::fake()->image('new_photo.png', 640, 480);

        $updated = $this->postService->update($post, [
            'content' => 'Content',
            'photos' => [$file],
        ]);

        $this->assertCount(1, $updated->photos);
        Storage::disk('photos')->assertExists($updated->photos->first()->path);
    }

    public function test_delete_removes_photos_from_filesystem(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'To be deleted',
        ]);

        $file1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('photo2.png', 640, 480);
        $photos = $this->photoService->upload([$file1, $file2], $post);

        $paths = array_map(fn ($photo) => $photo->path, $photos);

        $this->postService->delete($post);

        foreach ($paths as $path) {
            Storage::disk('photos')->assertMissing($path);
        }
    }

    public function test_delete_removes_post_from_database(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'To be deleted',
        ]);

        $postId = $post->id;

        $this->postService->delete($post);

        $this->assertDatabaseMissing('posts', ['id' => $postId]);
    }

    public function test_delete_removes_photo_records_from_database(): void
    {
        $leader = Leader::factory()->create();
        $post = Post::create([
            'leader_id' => $leader->id,
            'content' => 'To be deleted',
        ]);

        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
        $photos = $this->photoService->upload([$file], $post);
        $photoId = $photos[0]->id;

        $this->postService->delete($post);

        $this->assertDatabaseMissing('photos', ['id' => $photoId]);
    }
}
