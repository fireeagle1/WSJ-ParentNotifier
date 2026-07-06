@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Leader Management</h2>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add Leader Form -->
        <div class="bg-white overflow-hidden shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Leader</h3>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('leaders.store') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="flex-1">
                    <label for="email" class="sr-only">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="Email address">
                </div>
                <div class="flex-1">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           minlength="8"
                           maxlength="128"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="Password (min 8 characters)">
                </div>
                <div class="flex-1">
                    <label for="display_name" class="sr-only">Display Name</label>
                    <input type="text"
                           id="display_name"
                           name="display_name"
                           value="{{ old('display_name') }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="Display name">
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Leader
                </button>
            </form>
        </div>

        <!-- Leaders Table -->
        <div class="bg-white overflow-hidden shadow-sm">
            @if ($leaders->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No leaders found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Visible to Parents
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($leaders as $leader)
                                <tr class="{{ !$leader->visible_to_parents ? 'bg-gray-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $leader->display_name }}
                                        @if ($leader->id === auth()->id())
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                                You
                                            </span>
                                        @endif
                                        @if ($leader->is_admin)
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $leader->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $leader->role ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="{{ route('leaders.toggle-visibility', $leader) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 border font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150
                                                    {{ $leader->visible_to_parents
                                                        ? 'bg-green-50 border-green-300 text-green-700 hover:bg-green-100'
                                                        : 'bg-gray-100 border-gray-300 text-gray-500 hover:bg-gray-200' }}">
                                                @if($leader->visible_to_parents)
                                                    ✓ Visible
                                                @else
                                                    ✗ Hidden
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($leader->id !== auth()->id())
                                            <form method="POST" action="{{ route('leaders.destroy', $leader) }}"
                                                  onsubmit="return confirm('Are you sure you want to remove this leader?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Remove
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Cannot remove self</span>
                                        @endif
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
@endsection
