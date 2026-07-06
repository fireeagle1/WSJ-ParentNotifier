@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
<style>
    #map { height: 280px; width: 100%; }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Back link --}}
        <div class="mb-4">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Posts
            </a>
        </div>

        {{-- Composer Card --}}
        <div class="bg-white shadow-sm border border-gray-100 overflow-hidden">
            {{-- Composer Header --}}
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->display_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ Auth::user()->display_name }}</p>
                        <p class="text-xs text-gray-500">Creating a new post</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Post Content (borderless textarea like social media) --}}
                <div class="px-5 pt-4">
                    <textarea id="content"
                              name="content"
                              rows="5"
                              maxlength="5000"
                              required
                              class="w-full border-0 p-0 text-gray-800 text-base leading-relaxed placeholder-gray-400 focus:ring-0 resize-none"
                              placeholder="What's happening at the Jamboree?">{{ old('content') }}</textarea>
                    <div class="flex items-center justify-between mt-2 pb-3 border-b border-gray-100">
                        <span class="text-xs text-gray-400"><span id="char-count">0</span> / 5000</span>
                        @error('content')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Photo Upload Area --}}
                <div class="px-5 py-4 border-b border-gray-100">
                    <div id="photo-drop-zone"
                         class="relative border-2 border-dashed border-gray-200 p-6 text-center hover:border-brand-300 hover:bg-brand-50/30 transition cursor-pointer"
                         onclick="document.getElementById('photos').click()">
                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Click to add photos</p>
                        <p class="text-xs text-gray-400 mt-1">JPEG, PNG or WebP · Max 10MB each · Up to 10 photos</p>
                        <input type="file"
                               id="photos"
                               name="photos[]"
                               multiple
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden" />
                    </div>
                    @error('photos')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="photo-preview" class="mt-3 flex flex-wrap gap-2"></div>
                </div>

                {{-- Map Pin --}}
                <div class="px-5 py-4 border-b border-gray-100" x-data="{ showMap: {{ old('latitude') ? 'true' : 'false' }} }">
                    <div class="flex items-center gap-3 flex-wrap">
                        <button type="button"
                                @click="showMap = !showMap"
                                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-brand-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-text="showMap ? 'Hide Map' : 'Add Location Pin'"></span>
                        </button>
                        <button type="button"
                                id="use-my-location"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-brand-700 bg-brand-50 hover:bg-brand-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Use My Location
                        </button>
                    </div>

                    <div x-show="showMap" x-transition class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">Click on the map to place a pin, or use "Use My Location" to tag your current GPS position.</p>
                        <div id="map"></div>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                        <div class="mt-2 flex items-center gap-3">
                            <span id="coordinates-display" class="text-xs text-gray-500">No pin placed</span>
                            <button type="button"
                                    id="clear-pin"
                                    class="text-xs text-red-500 hover:text-red-700 hidden">
                                Remove
                            </button>
                        </div>
                    </div>
                    @error('latitude')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pin Post Toggle --}}
                <div class="px-5 py-4 border-b border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="is_pinned"
                               value="1"
                               {{ old('is_pinned') ? 'checked' : '' }}
                               class="w-5 h-5 border-gray-300 text-brand-600 focus:ring-brand-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900">📌 Pin this post</span>
                            <p class="text-xs text-gray-500">Pinned posts stay at the top of the feed</p>
                        </div>
                    </label>
                </div>

                {{-- Notification Toggle --}}
                <div class="px-5 py-4 border-b border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="send_notification"
                               value="1"
                               checked
                               class="w-5 h-5 border-gray-300 text-brand-600 focus:ring-brand-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Notify parents</span>
                            <p class="text-xs text-gray-500">Send email notification when this post is published</p>
                        </div>
                    </label>
                </div>

                {{-- Submit Footer --}}
                <div class="px-5 py-4 bg-gray-50/50 flex items-center justify-between">
                    <a href="{{ route('posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 font-medium text-sm text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Publish Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const content = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    function updateCharCount() {
        charCount.textContent = content.value.length;
    }
    content.addEventListener('input', updateCharCount);
    updateCharCount();

    // Auto-resize textarea
    content.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Photo preview
    const photosInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photo-preview');
    const dropZone = document.getElementById('photo-drop-zone');

    photosInput.addEventListener('change', function() {
        photoPreview.innerHTML = '';
        const files = Array.from(this.files).slice(0, 10);
        if (files.length > 0) {
            dropZone.classList.add('border-brand-300', 'bg-brand-50/30');
        }
        files.forEach(function(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative w-20 h-20 overflow-hidden';
                    div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" />';
                    photoPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Leaflet Map
    const mapEl = document.getElementById('map');
    if (mapEl) {
        const map = L.map('map').setView([52.0, -2.0], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        let marker = null;
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const coordsDisplay = document.getElementById('coordinates-display');
        const clearPinBtn = document.getElementById('clear-pin');

        // Restore pin from old values
        if (latInput.value && lngInput.value) {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            marker = L.marker([lat, lng]).addTo(map);
            coordsDisplay.textContent = 'Pin: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
            clearPinBtn.classList.remove('hidden');
            map.setView([lat, lng], 10);
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            latInput.value = lat;
            lngInput.value = lng;
            coordsDisplay.textContent = 'Pin: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
            clearPinBtn.classList.remove('hidden');
        });

        clearPinBtn.addEventListener('click', function() {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            latInput.value = '';
            lngInput.value = '';
            coordsDisplay.textContent = 'No pin placed';
            clearPinBtn.classList.add('hidden');
        });

        // Fix map rendering in hidden container
        setTimeout(function() { map.invalidateSize(); }, 300);

        // Use My Location button
        document.getElementById('use-my-location').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Locating...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }

                    latInput.value = lat;
                    lngInput.value = lng;
                    coordsDisplay.textContent = 'Pin: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
                    clearPinBtn.classList.remove('hidden');
                    map.setView([lat, lng], 14);

                    // Show the map if hidden
                    var mapSection = document.querySelector('[x-data]');
                    if (mapSection && mapSection.__x) {
                        mapSection.__x.$data.showMap = true;
                    }

                    document.getElementById('use-my-location').disabled = false;
                    document.getElementById('use-my-location').innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Location Set ✓';

                    setTimeout(function() { map.invalidateSize(); }, 100);
                },
                function(error) {
                    alert('Unable to get your location: ' + error.message);
                    document.getElementById('use-my-location').disabled = false;
                    document.getElementById('use-my-location').innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Use My Location';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    }
});
</script>
@endpush
