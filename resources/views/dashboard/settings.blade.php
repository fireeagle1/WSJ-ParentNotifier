@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Settings</h2>

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

        <!-- Profile Section -->
        <div class="bg-white overflow-hidden shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Profile</h3>
            <p class="text-sm text-gray-500 mb-4">This information is shown on posts and visible to parents on the "Meet the Leaders" page.</p>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('leaders.update-profile') }}" enctype="multipart/form-data" class="max-w-lg">
                @csrf

                <!-- Photo -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        @if(auth()->user()->photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->photo_path) }}"
                                 alt="Profile"
                                 class="w-16 h-16 object-cover border-2 border-purple-200">
                        @else
                            <div class="w-16 h-16 bg-purple-100 flex items-center justify-center">
                                <span class="text-purple-700 font-bold text-xl">{{ substr(auth()->user()->display_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <input type="file"
                               name="photo"
                               accept="image/jpeg,image/png,image/webp"
                               class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, or WebP. Max 5 MB.</p>
                </div>

                <!-- Display Name -->
                <div class="mb-4">
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text"
                           id="display_name"
                           name="display_name"
                           value="{{ old('display_name', auth()->user()->display_name) }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <input type="text"
                           id="role"
                           name="role"
                           value="{{ old('role', auth()->user()->role) }}"
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g. Scout Leader, Assistant Leader, Camp Warden">
                </div>

                <!-- Bio -->
                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio"
                              name="bio"
                              rows="3"
                              maxlength="1000"
                              class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="A short bio about yourself that parents can read...">{{ old('bio', auth()->user()->bio) }}</textarea>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', auth()->user()->phone) }}"
                           maxlength="50"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="+44 7xxx xxx xxx">
                </div>

                <!-- Email (read-only) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-sm text-gray-600 bg-gray-50 px-3 py-2 border border-gray-200">{{ auth()->user()->email }}</p>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Branding Section -->
        <div class="bg-white overflow-hidden shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Branding</h3>
            <p class="text-sm text-gray-500 mb-4">Customise the portal's appearance for parents. The header image appears at the top of the parent feed.</p>

            <!-- Current Header Preview (outside the branding form) -->
            @if($headerImagePath)
                <div class="mb-4 max-w-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Header Image</label>
                    <img src="{{ asset('storage/' . $headerImagePath) }}" alt="Header" class="w-full h-24 object-cover border border-gray-200">
                    <form method="POST" action="{{ route('leaders.remove-header-image') }}" class="mt-2" onsubmit="return confirm('Remove the header image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remove Header Image
                        </button>
                    </form>
                </div>
            @endif

            <form method="POST" action="{{ route('leaders.update-branding') }}" enctype="multipart/form-data" class="max-w-lg">
                @csrf

                <!-- Logo Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @php $logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value'); @endphp
                    @if($logoPath)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-10 w-10 object-contain bg-purple-100 p-1">
                            <span class="text-xs text-gray-500">Current logo</span>
                        </div>
                    @endif
                    <input type="file"
                           name="logo"
                           accept="image/jpeg,image/png,image/webp,image/svg+xml"
                           class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">PNG, SVG, JPEG, or WebP. Max 2 MB. Square works best (e.g. 200×200px).</p>
                </div>

                <!-- Header Image Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header Image</label>
                    <input type="file"
                           name="header_image"
                           accept="image/jpeg,image/png,image/webp"
                           class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, or WebP. Max 5 MB. Recommended: 1200×200px.</p>
                </div>

                <!-- Brand Name -->
                <div class="mb-4">
                    <label for="brand_name" class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                    <input type="text"
                           id="brand_name"
                           name="brand_name"
                           value="{{ old('brand_name', $brandName) }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g. 1st Exampletown Scouts">
                </div>

                <!-- Brand Color -->
                <div class="mb-4">
                    <label for="brand_color" class="block text-sm font-medium text-gray-700 mb-1">Brand Colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color"
                               id="brand_color"
                               name="brand_color"
                               value="{{ old('brand_color', $brandColor) }}"
                               class="h-10 w-14 border border-gray-300 cursor-pointer">
                        <span class="text-sm text-gray-500">Default: #7413dc (Scouts Purple)</span>
                    </div>
                </div>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Update Branding
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
