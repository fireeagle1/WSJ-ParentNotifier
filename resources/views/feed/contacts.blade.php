@extends('layouts.feed')

@section('title', 'Home Contact')

@section('content')
    <h2 class="text-xl font-bold text-gray-900 mb-2">Contact Us</h2>
    <p class="text-sm text-gray-500 mb-6">How to contact the team during the Jamboree.</p>

    @if($contacts->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">No home contact schedule has been published yet.</p>
        </div>
    @else
        @php
            // Group consecutive dates by leader into date ranges
            $ranges = collect();
            $currentRange = null;

            foreach ($contacts as $contact) {
                if ($currentRange &&
                    $currentRange['leader_id'] === $contact->leader_id &&
                    $currentRange['phone'] === ($contact->leader->phone ?: $contact->phone) &&
                    $contact->date->diffInDays($currentRange['end_date']) === 1) {
                    $currentRange['end_date'] = $contact->date;
                    $currentRange['includes_today'] = $currentRange['includes_today'] || $contact->date->isToday();
                } else {
                    if ($currentRange) {
                        $ranges->push($currentRange);
                    }
                    $currentRange = [
                        'leader_id' => $contact->leader_id,
                        'leader' => $contact->leader,
                        'start_date' => $contact->date,
                        'end_date' => $contact->date,
                        'phone' => $contact->leader->phone ?: $contact->phone,
                        'email' => $contact->leader->email,
                        'notes' => $contact->notes,
                        'includes_today' => $contact->date->isToday(),
                    ];
                }
            }
            if ($currentRange) {
                $ranges->push($currentRange);
            }

            // Find the current/active contact (today) or next upcoming
            $currentContact = $ranges->first(fn($r) => $r['includes_today']);
            $nextContact = !$currentContact ? $ranges->first(fn($r) => $r['start_date']->isFuture()) : null;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left column: Info boxes -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Please use the home contact -->
                <div class="bg-white border-l-4 border-purple-600 p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Please use the home contact</h3>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        Please do not contact leaders in country directly unless you have been specifically asked to do so.
                        Leaders in country may be supporting young people, travelling, or managing the programme.
                    </p>
                    <p class="text-sm text-gray-700 mt-2 leading-relaxed">
                        The home contact is the preferred point of contact for parents and guardians.
                    </p>
                </div>

                <!-- What to include when contacting us -->
                <div class="bg-white border-l-4 border-gray-300 p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">What to include when contacting us</h3>
                    <ul class="space-y-1.5 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="text-purple-600 mt-0.5">•</span>
                            Your name and relationship to the young person.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-600 mt-0.5">•</span>
                            The young person's name and team name.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-600 mt-0.5">•</span>
                            A clear description of what you need help with.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-600 mt-0.5">•</span>
                            The best number or email for us to contact you back on.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right column: Current home contact -->
            <div>
                <div class="bg-white border border-gray-200 p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Current home contact</h3>

                    @if($currentContact)
                        <div class="text-center">
                            @if($currentContact['leader']->photo_path)
                                <img src="{{ asset('storage/' . $currentContact['leader']->photo_path) }}"
                                     alt="{{ $currentContact['leader']->display_name }}"
                                     class="w-20 h-20 object-cover mx-auto mb-3">
                            @else
                                <div class="w-20 h-20 bg-purple-100 flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-purple-600">{{ substr($currentContact['leader']->display_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <p class="font-semibold text-gray-900">{{ $currentContact['leader']->display_name }}</p>
                            @if($currentContact['leader']->role)
                                <p class="text-xs text-gray-500">{{ $currentContact['leader']->role }}</p>
                            @endif

                            <div class="mt-3 space-y-2">
                                <a href="tel:{{ $currentContact['phone'] }}"
                                   class="block w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition text-center">
                                    📞 {{ $currentContact['phone'] }}
                                </a>
                                <a href="mailto:{{ $currentContact['email'] }}"
                                   class="block w-full px-4 py-2 border border-purple-200 text-purple-700 text-sm font-medium hover:bg-purple-50 transition text-center truncate">
                                    ✉️ {{ $currentContact['email'] }}
                                </a>
                            </div>

                            <p class="text-xs text-gray-500 mt-3">
                                @if($currentContact['start_date']->equalTo($currentContact['end_date']))
                                    On duty: {{ $currentContact['start_date']->format('jS M') }}
                                @else
                                    On duty: {{ $currentContact['start_date']->format('jS') }} to {{ $currentContact['end_date']->format('jS M') }}
                                @endif
                            </p>
                        </div>
                    @elseif($nextContact)
                        <div class="text-center">
                            @if($nextContact['leader']->photo_path)
                                <img src="{{ asset('storage/' . $nextContact['leader']->photo_path) }}"
                                     alt="{{ $nextContact['leader']->display_name }}"
                                     class="w-20 h-20 object-cover mx-auto mb-3">
                            @else
                                <div class="w-20 h-20 bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-gray-400">{{ substr($nextContact['leader']->display_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <p class="font-semibold text-gray-900">{{ $nextContact['leader']->display_name }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">Next home contact</span>

                            <p class="text-xs text-gray-500 mt-3">
                                Starts: {{ $nextContact['start_date']->format('jS M Y') }}
                            </p>

                            <p class="text-sm text-gray-600 mt-3">
                                No home contact is currently scheduled, but the next home contact is listed above.
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center">No current or upcoming home contact scheduled.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Full schedule -->
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Full schedule</h3>
            <div class="space-y-2">
                @foreach($ranges as $range)
                    <div class="bg-white border border-gray-200 p-4 shadow-sm flex items-center justify-between {{ $range['includes_today'] ? 'ring-2 ring-purple-400' : '' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                @if($range['start_date']->equalTo($range['end_date']))
                                    <span class="text-sm font-semibold text-gray-900">{{ $range['start_date']->format('jS M') }}</span>
                                @else
                                    <span class="text-sm font-semibold text-gray-900">{{ $range['start_date']->format('jS') }} to {{ $range['end_date']->format('jS M') }}</span>
                                @endif
                                @if($range['includes_today'])
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-purple-600 text-white">NOW</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mt-0.5">
                                <span class="font-medium">{{ $range['leader']->display_name }}</span>
                                <span class="text-gray-400 mx-1">·</span>
                                <span class="text-gray-500">{{ $range['leader']->email }}</span>
                            </p>
                        </div>
                        <a href="tel:{{ $range['phone'] }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $range['phone'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Warning -->
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
