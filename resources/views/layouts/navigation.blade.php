<div x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">

    <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-ref="dialog" aria-modal="true">
        
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80"></div>

        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-gray-900 pt-5 pb-4">
                
                <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute top-0 right-0 -mr-12 pt-2">
                    <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Logo -->
                <div class="flex shrink-0 items-center px-4">
                    <a href="{{ route(auth()->user()->homeRouteName()) }}" class="flex items-center space-x-2">
                        <x-application-logo class="block h-8 w-auto fill-current text-white" />
                        <span class="text-white font-bold text-xl">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>
                
                <!-- Mobile Navigation -->
                <nav class="mt-8 h-full flex-1 flex flex-col">
                    <div class="space-y-1 px-2 mb-4">
                        @include('layouts.navigation-links', ['mobile' => true])
                    </div>
                </nav>
            </div>
            <div class="w-14 shrink-0" aria-hidden="true">
                <!-- Dummy element to force sidebar to shrink to fit close icon -->
            </div>
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col" :class="{'lg:w-20': !desktopSidebarOpen, 'lg:w-64': desktopSidebarOpen}">
        <div class="flex flex-grow flex-col overflow-y-auto bg-gray-900 pb-4 transition-all duration-300 shadow-xl">
            <div class="flex h-16 shrink-0 items-center px-4 justify-between bg-gray-950">
                <a href="{{ route(auth()->user()->homeRouteName()) }}" class="flex items-center space-x-2 overflow-hidden truncate">
                    <x-application-logo class="block h-8 w-auto fill-current text-indigo-500 flex-shrink-0" />
                    <span x-show="desktopSidebarOpen" class="text-white font-bold text-lg whitespace-nowrap">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <button @click="desktopSidebarOpen = !desktopSidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <nav class="mt-8 flex-1 flex flex-col" aria-label="Sidebar">
                <div class="space-y-1 px-2">
                    @include('layouts.navigation-links', ['mobile' => false])
                </div>
            </nav>

            <!-- User Profile (Desktop) -->
            <div class="mt-auto px-4 pt-4 border-t border-gray-800">
                <x-dropdown align="top" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center w-full text-left focus:outline-none p-2 rounded-md hover:bg-gray-800 transition-colors">
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3 truncate" x-show="desktopSidebarOpen">
                                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs font-medium text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Mobile Top Header -->
    <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:hidden">
        <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
            <span class="sr-only">Open sidebar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="flex flex-1 justify-end gap-x-4 self-stretch lg:gap-x-6">
            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <!-- User Profile (Mobile) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

</div>
