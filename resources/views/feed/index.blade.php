@extends('layouts.feed')

@section('title', 'Expedition Updates')

@section('header', 'Expedition Updates')

@section('header-actions')
    <a href="/feed/{{ $token }}/map"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-purple-700 bg-purple-50 rounded-full hover:bg-purple-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
        </svg>
        Map View
    </a>
@endsection

@section('content')
    @if($posts->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-50 flex items-center justify-center">
                <svg class="h-8 w-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">No posts yet</p>
            <p class="mt-1 text-gray-400 text-sm">Check back soon for expedition updates.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($posts as $post)
                @include('feed._post', ['post' => $post])
            @endforeach
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <nav class="mt-6" aria-label="Pagination">
                <div class="flex items-center justify-center gap-2">
                    {{-- Previous Page Link --}}
                    @if($posts->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                            &laquo; Previous
                        </span>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}"
                           class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                            &laquo; Previous
                        </a>
                    @endif

                    {{-- Page Info --}}
                    <span class="px-3 py-2 text-sm text-gray-600">
                        Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}
                    </span>

                    {{-- Next Page Link --}}
                    @if($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}"
                           class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                            Next &raquo;
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                            Next &raquo;
                        </span>
                    @endif
                </div>
            </nav>
        @endif
    @endif
@endsection
