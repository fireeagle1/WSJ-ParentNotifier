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
                                {{ $leader->display_name }} ({{ $leader->email }})
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
                    <p class="mt-0.5 text-xs text-gray-400">Auto-fills from leader's profile</p>
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

        <!-- Schedule Table -->
        <div class="bg-white overflow-hidden shadow-sm">
            @if ($contacts->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No home contact entries scheduled yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leader</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($contacts as $contact)
                                <tr class="{{ $contact->date->isToday() ? 'bg-purple-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $contact->date->format('D, j M Y') }}
                                        @if($contact->date->isToday())
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800">Today</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $contact->leader->display_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $contact->leader->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $contact->phone }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $contact->notes ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="{{ route('contacts.destroy', $contact) }}"
                                              onsubmit="return confirm('Remove this entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
