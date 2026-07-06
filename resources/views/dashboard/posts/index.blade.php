@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
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
                    <article class="bg-white shadow-sm border {{ $post->is_pinned ? 'border-brand-200 ring-1 ring-brand-100' : 'border-gray-100' }} overflow-hidden">
                        {{-- Pinned badge --}}
                        @if($post->is_pinned)
                            <div class="px-4 py-2 bg-brand-50 border-b border-brand-100 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span class="text-xs font-semibold text-brand-700">Pinned</span>
                            </div>
                        @endif

                        {{-- Map as hero image when no photos but location exists --}}
                        @if($post->photos->isEmpty() && $post->latitude && $post->longitude)
                            <div class="w-full"
                                 id="map-post-{{ $post->id }}"
                                 style="height: 200px;"
                                 data-lat="{{ $post->latitude }}"
                                 data-lng="{{ $post->longitude }}">
                            </div>
                        @endif

                        <div class="p-4 sm:p-5">
                            {{-- Post Header: Author, Timestamp & Actions --}}
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    @if($post->leader->photo_path)
                                        <img src="{{ asset('storage/' . $post->leader->photo_path) }}"
                                             alt="{{ $post->leader->display_name }}"
                                             class="w-10 h-10 object-cover ring-2 ring-brand-100">
                                    @else
                                        <div class="w-10 h-10 bg-wsj-navy flex items-center justify-center text-white font-semibold text-sm ring-2 ring-blue-100">
                                            {{ strtoupper(substr($post->leader->display_name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $post->leader->display_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }} · {{ $post->created_at->format('j M Y, g:i A') }}</p>
                                    </div>
                                </div>

                                {{-- Edit / Delete actions --}}
                                <div class="flex items-center gap-1">
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

                            {{-- Post content (full, not truncated) --}}
                            <div class="mt-3 text-gray-800 text-[15px] leading-relaxed whitespace-pre-wrap">{{ $post->content }}</div>

                            {{-- Photos (full-size grid with lightbox) --}}
                            @if($post->photos->isNotEmpty())
                                <div class="grid {{ $post->photos->count() === 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-1.5 mt-4 overflow-hidden">
                                    @foreach($post->photos as $photo)
                                        <button type="button"
                                                class="aspect-square overflow-hidden cursor-pointer hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 {{ $post->photos->count() === 1 ? 'aspect-video' : '' }}"
                                                onclick="openLightbox('{{ asset('storage/photos/' . $photo->filename) }}')"
                                                aria-label="View full size photo">
                                            <img src="{{ asset('storage/photos/' . $photo->filename) }}"
                                                 alt="Post photo"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy">
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Small inline map below photos if location exists --}}
                                @if($post->latitude && $post->longitude)
                                    <div class="overflow-hidden border border-gray-100 mt-4"
                                         id="map-post-{{ $post->id }}"
                                         style="height: 150px;"
                                         data-lat="{{ $post->latitude }}"
                                         data-lng="{{ $post->longitude }}">
                                    </div>
                                @endif
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(method_exists($posts, 'hasPages') && $posts->hasPages())
                <nav class="mt-6" aria-label="Pagination">
                    {{ $posts->links() }}
                </nav>
            @endif
        @endif
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img" src="" alt="Full size photo">
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
<style>
    .lightbox-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.85);
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .lightbox-overlay.active {
        display: flex;
    }
    .lightbox-overlay img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
    }
    .lightbox-close {
        position: absolute;
        top: 1rem;
        right: 1.5rem;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    // Lightbox
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.getElementById('lightbox-img').src = '';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });

    // Initialize maps
    document.querySelectorAll('[id^="map-post-"]').forEach(function(el) {
        var lat = parseFloat(el.dataset.lat);
        var lng = parseFloat(el.dataset.lng);
        if (!lat || !lng) return;

        var map = L.map(el, {
            scrollWheelZoom: false,
            dragging: !L.Browser.mobile
        }).setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
            maxZoom: 19
        }).addTo(map);

        L.marker([lat, lng]).addTo(map);
    });
</script>
@endpush
@endsection
