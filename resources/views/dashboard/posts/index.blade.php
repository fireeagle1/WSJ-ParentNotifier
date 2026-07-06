@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Posts</h2>
            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 font-medium text-sm text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Post
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="bg-white shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-brand-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium">No posts yet</p>
                <p class="text-gray-400 text-sm mt-1">Create your first post to share updates with parents.</p>
                <a href="{{ route('posts.create') }}"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-brand-600 font-medium text-sm text-white hover:bg-brand-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create First Post
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <article class="bg-white shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-5">
                            {{-- Post header: avatar, author, time --}}
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-brand-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($post->leader->display_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-900 text-sm">{{ $post->leader->display_name ?? 'Unknown' }}</p>
                                            @if($post->is_pinned)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-700">📌 Pinned</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                {{-- Actions dropdown --}}
                                <div class="flex items-center gap-1" x-data="{ open: false }">
                                    <a href="{{ route('posts.edit', $post) }}"
                                       class="p-2 text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                          onsubmit="return confirm('Delete this post? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Post content --}}
                            <div class="mt-3 text-gray-800 text-sm leading-relaxed whitespace-pre-line">{{ Str::limit($post->content, 300) }}</div>

                            {{-- Photo thumbnails --}}
                            @if($post->photos->count() > 0)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($post->photos->take(4) as $photo)
                                        <div class="relative w-20 h-20 overflow-hidden bg-gray-100">
                                            <img src="{{ asset('storage/photos/' . $photo->filename) }}"
                                                 alt="Post photo"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        </div>
                                    @endforeach
                                    @if($post->photos->count() > 4)
                                        <div class="w-20 h-20 bg-gray-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-500">+{{ $post->photos->count() - 4 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Post meta badges --}}
                            <div class="mt-3 flex items-center gap-2 flex-wrap">
                                @if ($post->latitude && $post->longitude)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        Location
                                    </span>
                                @endif
                                @if ($post->photos->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-brand-50 text-brand-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $post->photos->count() }} {{ Str::plural('photo', $post->photos->count()) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(method_exists($posts, 'hasPages') && $posts->hasPages())
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
