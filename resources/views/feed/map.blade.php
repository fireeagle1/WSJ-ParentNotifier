@extends('layouts.feed')

@section('title', 'Expedition Map')

@section('header', 'Expedition Map')

@section('header-actions')
    <a href="/feed/{{ $token }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Feed
    </a>
@endsection

@section('content')
    @if($posts->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="mt-4 text-gray-500 text-sm">No map pins have been added to posts yet.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div id="combined-map" style="height: 500px; width: 100%;"></div>
        </div>

        <p class="mt-3 text-xs text-gray-500 text-center">
            Click a marker to see the post details. {{ $posts->count() }} location{{ $posts->count() !== 1 ? 's' : '' }} pinned.
        </p>
    @endif
@endsection

@push('scripts')
@if($posts->isNotEmpty())
<script>
    (function() {
        var map = L.map('combined-map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        var markers = [];
        var posts = @json($mapPins);

        posts.forEach(function(post) {
            var marker = L.marker([post.lat, post.lng]).addTo(map);
            marker.bindPopup(
                '<div class="text-sm">' +
                    '<p class="font-semibold mb-1">' + escapeHtml(post.author) + '</p>' +
                    '<p class="text-gray-600 mb-1">' + escapeHtml(post.content) + '</p>' +
                    '<p class="text-xs text-gray-500 mb-2">' + escapeHtml(post.date) + '</p>' +
                    '<a href="' + post.url + '" class="text-green-600 hover:underline text-xs font-medium">View Post &rarr;</a>' +
                '</div>'
            );
            markers.push(marker);
        });

        // Fit map bounds to all markers
        if (markers.length > 0) {
            var group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    })();
</script>
@endif
@endpush
