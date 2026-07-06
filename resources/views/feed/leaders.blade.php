@extends('layouts.feed')

@section('title', 'Meet the Leaders')

@section('content')
    <h2 class="text-xl font-bold text-gray-900 mb-6">Meet the Leaders</h2>

    @if($leaders->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">No leader profiles available yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($leaders as $leader)
                <div class="bg-white shadow-sm border border-gray-200 p-5">
                    <div class="flex items-start gap-4">
                        @if($leader->photo_path)
                            <img src="{{ asset('storage/' . $leader->photo_path) }}"
                                 alt="{{ $leader->display_name }}"
                                 class="w-16 h-16 object-cover flex-shrink-0 border-2 border-purple-200">
                        @else
                            <div class="w-16 h-16 bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-700 font-bold text-xl">{{ substr($leader->display_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $leader->display_name }}</h3>
                            @if($leader->role)
                                <p class="text-sm text-purple-700 font-medium">{{ $leader->role }}</p>
                            @endif
                            @if($leader->bio)
                                <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $leader->bio }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
