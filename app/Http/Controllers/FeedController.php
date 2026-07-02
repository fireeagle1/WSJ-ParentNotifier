<?php

namespace App\Http\Controllers;

use App\Models\HomeContact;
use App\Models\Leader;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    /**
     * Display the paginated feed of posts for a parent.
     *
     * Posts are shown in reverse chronological order with photos and leader info.
     */
    public function index(Request $request, string $token): View
    {
        $posts = Post::with('photos', 'leader')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('feed.index', [
            'posts' => $posts,
            'token' => $token,
        ]);
    }

    /**
     * Display the combined map view with all geo-tagged posts.
     *
     * Only loads posts that have both latitude and longitude set.
     */
    public function map(Request $request, string $token): View
    {
        $posts = Post::with('leader')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('created_at', 'desc')
            ->get();

        $mapPins = $posts->map(function ($post) use ($token) {
            return [
                'id' => $post->id,
                'lat' => (float) $post->latitude,
                'lng' => (float) $post->longitude,
                'content' => \Illuminate\Support\Str::limit($post->content, 100),
                'author' => $post->leader->display_name,
                'date' => $post->created_at->format('j M Y, g:i A'),
                'url' => '/feed/' . $token . '#post-' . $post->id,
            ];
        })->values();

        return view('feed.map', [
            'posts' => $posts,
            'mapPins' => $mapPins,
            'token' => $token,
        ]);
    }

    /**
     * Display the "Meet the Leaders" page for parents.
     */
    public function leaders(Request $request, string $token): View
    {
        $leaders = Leader::orderBy('display_name')->get();

        return view('feed.leaders', [
            'leaders' => $leaders,
            'token' => $token,
        ]);
    }

    /**
     * Display the home contact schedule for parents.
     */
    public function contacts(Request $request, string $token): View
    {
        $contacts = HomeContact::with('leader')
            ->where('date', '>=', now()->subDay())
            ->orderBy('date', 'asc')
            ->get();

        return view('feed.contacts', [
            'contacts' => $contacts,
            'token' => $token,
        ]);
    }
}
