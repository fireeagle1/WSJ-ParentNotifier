<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
    ) {}

    /**
     * Display a listing of all posts.
     */
    public function index(): View
    {
        $posts = Post::with('photos', 'leader')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('dashboard.posts.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['file', 'max:10240', 'mimes:jpeg,png,webp'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'send_notification' => ['nullable'],
            'is_pinned' => ['nullable'],
        ]);

        $sendNotification = $request->has('send_notification');

        $this->postService->create([
            'content' => $validated['content'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'photos' => $request->file('photos', []),
            'send_notification' => $sendNotification,
            'is_pinned' => $request->has('is_pinned'),
        ], $request->user());

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $post->load('photos');

        return view('dashboard.posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['file', 'max:10240', 'mimes:jpeg,png,webp'],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => ['integer', 'exists:photos,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_pinned' => ['nullable'],
        ]);

        $this->postService->update($post, [
            'content' => $validated['content'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'photos' => $request->file('photos', []),
            'remove_photos' => $validated['remove_photos'] ?? [],
            'is_pinned' => $request->has('is_pinned'),
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->delete($post);

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
