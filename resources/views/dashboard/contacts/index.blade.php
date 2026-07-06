@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Home Contact Schedule</h2>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Contact Entry Form -->
        <div class="bg-white overflow-hidden shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Add Schedule Entry</h3>
            <p class="text-sm text-gray-500 mb-4">Select a date range to schedule a leader across multiple days at once.</p>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contacts.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                @csrf
                <div>
                    <label for="leader_id" class="block text-xs font-medium text-gray-500 mb-1">Leader</label>
                    <select id="leader_id"
                            name="leader_id"
                            required
                            class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            onchange="autoFillPhone(this)">
                        <option value="">Select leader...</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}" data-phone="{{ $leader->phone }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                {{ $leader->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ old('date_from') }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ old('date_to') }}"
                           required
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <p class="mt-0.5 text-xs text-gray-400">Same as "From" for a single day</p>
                </div>
                <div>
                    <label for="phone" class="block text-xs font-medium text-gray-500 mb-1">Phone Number</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone') }}"
                           required
                           maxlength="50"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Auto-filled from profile">
                </div>
                <div>
                    <label for="notes" class="block text-xs font-medium text-gray-500 mb-1">Notes (optional)</label>
                    <input type="text"
                           id="notes"
                           name="notes"
                           value="{{ old('notes') }}"
                           maxlength="500"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g. Available 6pm-10pm">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Add Entry
                    </button>
                </div>
            </form>
        </div>

        <!-- Schedule - Grouped by date ranges -->
        <div class="bg-white overflow-hidden shadow-sm">
            @if ($contacts->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No home contact entries scheduled yet.
                </div>
            @else
                @php
                    // Group consecutive dates by leader + phone into ranges
                    $ranges = collect();
                    $currentRange = null;

                    foreach ($contacts as $contact) {
                        if ($currentRange &&
                            $currentRange['leader_id'] === $contact->leader_id &&
                            $currentRange['phone'] === $contact->phone &&
                            $contact->date->diffInDays($currentRange['end_date']) === 1) {
                            $currentRange['end_date'] = $contact->date;
                            $currentRange['count']++;
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
                                'phone' => $contact->phone,
                                'notes' => $contact->notes,
                                'count' => 1,
                                'includes_today' => $contact->date->isToday(),
                            ];
                        }
                    }
                    if ($currentRange) {
                        $ranges->push($currentRange);
                    }
                @endphp

                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-4">{{ $contacts->count() }} total {{ Str::plural('day', $contacts->count()) }} scheduled across {{ $ranges->count() }} {{ Str::plural('block', $ranges->count()) }}.</p>

                    <div class="space-y-3">
                        @foreach($ranges as $range)
                            <div class="border border-gray-200 p-4 {{ $range['includes_today'] ? 'ring-2 ring-purple-400 bg-purple-50' : 'bg-gray-50' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if($range['start_date']->equalTo($range['end_date']))
                                                <span class="font-semibold text-gray-900">{{ $range['start_date']->format('D, j M Y') }}</span>
                                            @else
                                                <span class="font-semibold text-gray-900">{{ $range['start_date']->format('D j M') }} → {{ $range['end_date']->format('D j M Y') }}</span>
                                                <span class="text-xs text-gray-400">({{ $range['count'] }} days)</span>
                                            @endif
                                            @if($range['includes_today'])
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-purple-600 text-white">TODAY</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-700 mt-1">
                                            <span class="font-medium">{{ $range['leader']->display_name }}</span>
                                            <span class="text-gray-400 mx-1">·</span>
                                            <span class="text-gray-600">{{ $range['phone'] }}</span>
                                        </p>
                                        @if($range['notes'])
                                            <p class="text-xs text-gray-500 mt-1">{{ $range['notes'] }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('contacts.bulk-destroy') }}"
                                          onsubmit="return confirm('Remove {{ $range['count'] === 1 ? 'this entry' : 'all ' . $range['count'] . ' days in this block' }}?')">
                                        @csrf
                                        <input type="hidden" name="leader_id" value="{{ $range['leader_id'] }}">
                                        <input type="hidden" name="date_from" value="{{ $range['start_date']->toDateString() }}">
                                        <input type="hidden" name="date_to" value="{{ $range['end_date']->toDateString() }}">
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 border border-red-200 hover:bg-red-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function autoFillPhone(select) {
    var option = select.options[select.selectedIndex];
    var phone = option.getAttribute('data-phone');
    var phoneInput = document.getElementById('phone');
    if (phone && !phoneInput.value) {
        phoneInput.value = phone;
    }
}

// Auto-set "To Date" to match "From Date" when From is changed (if To is empty)
document.getElementById('date_from').addEventListener('change', function() {
    var toInput = document.getElementById('date_to');
    if (!toInput.value) {
        toInput.value = this.value;
    }
});
</script>
@endpush
@endsection
