<?php

namespace Tests\Unit\Services;

use App\Models\Leader;
use App\Models\Photo;
use App\Models\Post;
use App\Services\PhotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PhotoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PhotoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PhotoService();
        Storage::fake('photos');
    }

    protected function createPost(): Post
    {
        $leader = Leader::create([
            'email' => 'leader@example.com',
            'password' => bcrypt('password'),
            'display_name' => 'Test Leader',
            'is_admin' => true,
        ]);

        return Post::create([
            'leader_id' => $leader->id,
            'content' => 'Test post content',
        ]);
    }

    public function test_upload_stores_files_and_creates_photo_records(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(500),
            UploadedFile::fake()->image('photo2.png', 1024, 768)->size(1000),
        ];

        $photos = $this->service->upload($files, $post);

        $this->assertCount(2, $photos);
        $this->assertInstanceOf(Photo::class, $photos[0]);
        $this->assertInstanceOf(Photo::class, $photos[1]);

        // Verify files exist on the photos disk
        Storage::disk('photos')->assertExists($photos[0]->filename);
        Storage::disk('photos')->assertExists($photos[1]->filename);

        // Verify database records
        $this->assertEquals($post->id, $photos[0]->post_id);
        $this->assertEquals($post->id, $photos[1]->post_id);
    }

    public function test_upload_assigns_incremental_sort_order(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(500),
            UploadedFile::fake()->image('photo2.png', 1024, 768)->size(500),
            UploadedFile::fake()->image('photo3.webp', 640, 480)->size(500),
        ];

        $photos = $this->service->upload($files, $post);

        $this->assertEquals(0, $photos[0]->sort_order);
        $this->assertEquals(1, $photos[1]->sort_order);
        $this->assertEquals(2, $photos[2]->sort_order);
    }

    public function test_upload_continues_sort_order_from_existing_photos(): void
    {
        $post = $this->createPost();

        // Upload first batch
        $firstBatch = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(500),
        ];
        $this->service->upload($firstBatch, $post);

        // Upload second batch
        $secondBatch = [
            UploadedFile::fake()->image('photo2.jpg', 800, 600)->size(500),
        ];
        $photos = $this->service->upload($secondBatch, $post);

        $this->assertEquals(1, $photos[0]->sort_order);
    }

    public function test_upload_generates_unique_filenames(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(500),
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(500),
        ];

        $photos = $this->service->upload($files, $post);

        $this->assertNotEquals($photos[0]->filename, $photos[1]->filename);
    }

    public function test_upload_rejects_file_exceeding_10mb(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('large.jpg', 800, 600)->size(10241), // Just over 10 MB
        ];

        $this->expectException(ValidationException::class);
        $this->service->upload($files, $post);
    }

    public function test_upload_rejects_invalid_mime_type(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
        ];

        $this->expectException(ValidationException::class);
        $this->service->upload($files, $post);
    }

    public function test_upload_accepts_jpeg_png_webp(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo.jpg', 800, 600)->size(500),
            UploadedFile::fake()->image('photo.png', 800, 600)->size(500),
            UploadedFile::fake()->create('photo.webp', 500, 'image/webp'),
        ];

        $photos = $this->service->upload($files, $post);

        $this->assertCount(3, $photos);
    }

    public function test_upload_accepts_file_at_exactly_10mb(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('exact10mb.jpg', 800, 600)->size(10240), // Exactly 10 MB
        ];

        $photos = $this->service->upload($files, $post);

        $this->assertCount(1, $photos);
    }

    public function test_delete_removes_file_from_disk(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo.jpg', 800, 600)->size(500),
        ];

        $photos = $this->service->upload($files, $post);
        $photo = $photos[0];
        $filename = $photo->filename;

        Storage::disk('photos')->assertExists($filename);

        $this->service->delete($photo);

        Storage::disk('photos')->assertMissing($filename);
    }

    public function test_delete_removes_database_record(): void
    {
        $post = $this->createPost();
        $files = [
            UploadedFile::fake()->image('photo.jpg', 800, 600)->size(500),
        ];

        $photos = $this->service->upload($files, $post);
        $photo = $photos[0];
        $photoId = $photo->id;

        $this->service->delete($photo);

        $this->assertDatabaseMissing('photos', ['id' => $photoId]);
    }
}
