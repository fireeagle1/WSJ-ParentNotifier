@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit {{ $leader->display_name }}'s Profile</h2>
            <a href="{{ route('leaders.index') }}" class="text-sm text-brand-600 hover:text-brand-800 underline">
                ← Back to Leaders
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm p-6">
            <form method="POST" action="{{ route('leaders.update-bio', $leader) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Photo -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        @if($leader->photo_path)
                            <img src="{{ asset('storage/' . $leader->photo_path) }}"
                                 alt="{{ $leader->display_name }}"
                                 class="w-16 h-16 object-cover border-2 border-brand-200">
                        @else
                            <div class="w-16 h-16 bg-brand-100 flex items-center justify-center">
                                <span class="text-brand-700 font-bold text-xl">{{ substr($leader->display_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <input type="file"
                               name="photo"
                               accept="image/jpeg,image/png,image/webp"
                               class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, or WebP. Max 5 MB.</p>
                </div>

                <!-- Display Name -->
                <div class="mb-4">
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text"
                           id="display_name"
                           name="display_name"
                           value="{{ old('display_name', $leader->display_name) }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <input type="text"
                           id="role"
                           name="role"
                           value="{{ old('role', $leader->role) }}"
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="e.g. Scout Leader, Assistant Leader">
                </div>

                <!-- Bio -->
                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio"
                              name="bio"
                              rows="4"
                              maxlength="1000"
                              class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                              placeholder="A short bio about this leader...">{{ old('bio', $leader->bio) }}</textarea>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $leader->phone) }}"
                           maxlength="50"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="+44 7xxx xxx xxx">
                </div>

                <!-- Email (read-only) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-sm text-gray-600 bg-gray-50 px-3 py-2 border border-gray-200">{{ $leader->email }}</p>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Update Profile
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
