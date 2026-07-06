<article id="post-{{ $post->id }}" class="bg-white shadow-sm border {{ $post->is_pinned ? 'border-purple-200 ring-1 ring-purple-100' : 'border-gray-100' }} overflow-hidden">
    {{-- Pinned badge --}}
    @if($post->is_pinned)
        <div class="px-4 py-2 bg-purple-50 border-b border-purple-100 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            <span class="text-xs font-semibold text-purple-700">Pinned</span>
        </div>
    @endif

    {{-- Map as hero image when no photos but location exists --}}
    @if($post->photos->isEmpty() && $post->latitude && $post->longitude)
        <div class="w-full"
             id="map-post-{{ $post->id }}"
             style="height: 220px;"
             data-lat="{{ $post->latitude }}"
             data-lng="{{ $post->longitude }}">
        </div>

        @push('scripts')
        <script>
            (function() {
                var el = document.getElementById('map-post-{{ $post->id }}');
                var lat = parseFloat(el.dataset.lat);
                var lng = parseFloat(el.dataset.lng);

                var map = L.map(el, {
                    scrollWheelZoom: false,
                    dragging: !L.Browser.mobile
                }).setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                L.marker([lat, lng]).addTo(map);
            })();
        </script>
        @endpush
    @endif

    <div class="p-4 sm:p-5">
        <!-- Post Header: Author & Timestamp -->
        <div class="flex items-center gap-3 mb-3">
            @if($post->leader->photo_path)
                <img src="{{ asset('storage/' . $post->leader->photo_path) }}"
                     alt="{{ $post->leader->display_name }}"
                     class="w-10 h-10 object-cover ring-2 ring-purple-100">
            @else
                <div class="w-10 h-10 bg-[#7413dc] flex items-center justify-center text-white font-semibold text-sm ring-2 ring-purple-100">
                    {{ strtoupper(substr($post->leader->display_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $post->leader->display_name }}</p>
                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }} · {{ $post->created_at->format('j M Y, g:i A') }}</p>
            </div>
        </div>

        <!-- Post Content -->
        <div class="text-gray-800 text-[15px] leading-relaxed whitespace-pre-wrap mb-4">{{ $post->content }}</div>

        <!-- Photos (thumbnails) -->
        @if($post->photos->isNotEmpty())
            <div class="grid {{ $post->photos->count() === 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-1.5 mb-4 overflow-hidden">
                @foreach($post->photos as $photo)
                    <button type="button"
                            class="aspect-square overflow-hidden cursor-pointer hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 {{ $post->photos->count() === 1 ? 'aspect-video' : '' }}"
                            onclick="openLightbox('{{ asset('storage/photos/' . $photo->filename) }}')"
                            aria-label="View full size photo">
                        <img src="{{ asset('storage/photos/' . $photo->filename) }}"
                             alt="Post photo"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </button>
                @endforeach
            </div>

            {{-- Show small inline map below photos if location exists --}}
            @if($post->latitude && $post->longitude)
                <div class="overflow-hidden border border-gray-100 mb-4"
                     id="map-post-{{ $post->id }}"
                     style="height: 160px;"
                     data-lat="{{ $post->latitude }}"
                     data-lng="{{ $post->longitude }}">
                </div>

                @push('scripts')
                <script>
                    (function() {
                        var el = document.getElementById('map-post-{{ $post->id }}');
                        var lat = parseFloat(el.dataset.lat);
                        var lng = parseFloat(el.dataset.lng);

                        var map = L.map(el, {
                            scrollWheelZoom: false,
                            dragging: !L.Browser.mobile
                        }).setView([lat, lng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19
                        }).addTo(map);

                        L.marker([lat, lng]).addTo(map);
                    })();
                </script>
                @endpush
            @endif
        @endif
    </div>
</article>
