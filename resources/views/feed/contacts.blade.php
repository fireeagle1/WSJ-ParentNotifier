@extends('layouts.feed')

@section('title', 'Home Contact')

@section('content')
    <h2 class="text-xl font-bold text-gray-900 mb-2">Home Contact</h2>
    <p class="text-sm text-gray-500 mb-6">
        Below is the schedule of who is available as the home contact during the Jamboree.
        Please <strong>do not contact young people directly</strong> while they are away — use the home contact listed below.
    </p>

    @if($contacts->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">No home contact schedule has been published yet.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($contacts as $contact)
                <div class="bg-white shadow-sm border border-gray-200 p-4 {{ $contact->date->isToday() ? 'ring-2 ring-purple-400' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $contact->date->format('l, j M Y') }}</span>
                                @if($contact->date->isToday())
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-purple-600 text-white">TODAY</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mt-1">
                                <span class="font-medium">{{ $contact->leader->display_name }}</span>
                                @if($contact->leader->role)
                                    <span class="text-gray-400">· {{ $contact->leader->role }}</span>
                                @endif
                            </p>
                            @if($contact->notes)
                                <p class="text-xs text-gray-500 mt-1">{{ $contact->notes }}</p>
                            @endif
                        </div>
                        @php
                            $contactPhone = $contact->leader->phone ?: $contact->phone;
                        @endphp
                        <a href="tel:{{ $contactPhone }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $contactPhone }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 bg-amber-50 border border-amber-200">
            <div class="flex gap-2">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-sm text-amber-800">
                    <strong>Reminder:</strong> Please do not attempt to contact young people directly while they are at the Jamboree. Use the home contact number above if you need to get in touch.
                </p>
            </div>
        </div>
    @endif
@endsection
