<nav x-data="{ open: false }" class="bg-[#7413dc] border-b border-purple-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between" style="min-height: 220px;">
            <div class="flex">
                <!-- Portal Logo + Name -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        @php $logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value'); @endphp
                        @if($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="object-contain" style="height: 200px;">
                        @else
                            <x-application-logo class="fill-current text-white" style="height: 200px; width: 200px;" />
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('dashboard') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('posts.*') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Posts') }}
                    </a>
                    <a href="{{ route('parents.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('parents.*') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Parents') }}
                    </a>
                    @if(Auth::user()->is_admin)
                    <a href="{{ route('leaders.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('leaders.index') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Leaders') }}
                    </a>
                    @endif
                    <a href="{{ route('contacts.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('contacts.*') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Contacts') }}
                    </a>
                    <a href="{{ route('leaders.settings') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 {{ request()->routeIs('leaders.settings') ? 'text-white border-b-2 border-white' : 'text-purple-200 hover:text-white' }} transition duration-150 ease-in-out">
                        {{ __('Settings') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-purple-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->display_name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('leaders.settings')">
                            {{ __('Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-purple-200 hover:text-white hover:bg-purple-700 focus:outline-none focus:bg-purple-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-purple-800">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('posts.index') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('posts.*') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Posts') }}
            </a>
            <a href="{{ route('parents.index') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('parents.*') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Parents') }}
            </a>
            @if(Auth::user()->is_admin)
            <a href="{{ route('leaders.index') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('leaders.index') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Leaders') }}
            </a>
            @endif
            <a href="{{ route('contacts.index') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('contacts.*') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Contacts') }}
            </a>
            <a href="{{ route('leaders.settings') }}" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium {{ request()->routeIs('leaders.settings') ? 'text-white bg-purple-900 border-l-4 border-white' : 'text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent' }} transition duration-150 ease-in-out">
                {{ __('Settings') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-purple-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->display_name }}</div>
                <div class="font-medium text-sm text-purple-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium text-purple-200 hover:text-white hover:bg-purple-700 border-l-4 border-transparent transition duration-150 ease-in-out">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
