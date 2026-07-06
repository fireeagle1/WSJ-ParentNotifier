@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Parent Management</h2>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sign-Up Toggle -->
        <div class="bg-white overflow-hidden shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Sign-Up Link</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if ($isSignupEnabled && $signupToken)
                            Sign-ups are currently <span class="font-semibold text-green-600">enabled</span>.
                            Share this link with parents: <br>
                            <a href="{{ route('signup.show', $signupToken) }}" class="text-indigo-600 hover:text-indigo-800 underline text-xs break-all" target="_blank">{{ route('signup.show', $signupToken) }}</a>
                        @elseif ($isSignupEnabled)
                            Sign-ups are currently <span class="font-semibold text-green-600">enabled</span>.
                            <a href="{{ route('signup.show') }}" class="text-indigo-600 hover:text-indigo-800 underline" target="_blank">{{ route('signup.show') }}</a>
                        @else
                            Sign-ups are currently <span class="font-semibold text-red-600">disabled</span>.
                            Enabling will generate a fresh sign-up link.
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('parents.toggle-signup') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150
                            {{ $isSignupEnabled
                                ? 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500'
                                : 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500' }}
                            focus:outline-none focus:ring-2 focus:ring-offset-2">
                        {{ $isSignupEnabled ? 'Disable Sign-Ups' : 'Enable Sign-Ups' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Add Parent Form -->
        <div class="bg-white overflow-hidden shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Parent</h3>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('parents.store') }}" class="flex flex-col sm:flex-row gap-3">
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
                           placeholder="Parent email address">
                </div>
                <div class="flex-1">
                    <label for="young_person_name" class="sr-only">Young Person's Name</label>
                    <input type="text"
                           id="young_person_name"
                           name="young_person_name"
                           value="{{ old('young_person_name') }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="Young person's name">
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Parent
                </button>
            </form>
        </div>

        <!-- Parents Table -->
        <div class="bg-white overflow-hidden shadow-sm">
            @if ($parents->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No parents registered yet. Add one above or enable the sign-up link.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Young Person's Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Access Link
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($parents as $parent)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $parent->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $parent->young_person_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($parent->is_revoked)
                                            <span class="text-gray-400 italic">Revoked</span>
                                        @else
                                            <a href="{{ url('/feed/' . $parent->access_token) }}"
                                               class="text-brand-600 hover:text-brand-800 underline text-xs break-all"
                                               target="_blank">
                                                {{ url('/feed/' . $parent->access_token) }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($parent->is_revoked)
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                                Revoked
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if (!$parent->is_revoked)
                                            <form method="POST" action="{{ route('parents.destroy', $parent) }}"
                                                  onsubmit="return confirm('Are you sure you want to revoke access for this parent?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Revoke
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs italic">No actions available</span>
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
