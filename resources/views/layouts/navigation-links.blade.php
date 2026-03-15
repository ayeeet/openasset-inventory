@php
    $baseClasses = "group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors ";
    $activeClasses = "bg-gray-800 text-white";
    $inactiveClasses = "text-gray-300 hover:bg-gray-700 hover:text-white";
@endphp

@if(!auth()->user()->isEmployee())
<a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1', 'px-2': desktopSidebarOpen || '{{ $mobile }}' == '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Dashboard') }}</span>
</a>
@endif

@if(auth()->user()->hasAccess('assets'))
<a href="{{ route('assets.index') }}" class="{{ request()->routeIs('assets.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Assets') }}</span>
</a>
@endif

@if(auth()->user()->hasAccess('organization'))
    <div class="pt-4 pb-2" x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">
        <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Organization</p>
    </div>
    
    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
        <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Departments') }}</span>
    </a>

    <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
        <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Locations') }}</span>
    </a>
@endif

@if(auth()->user()->hasAccess('people'))
<div class="pt-4 pb-2" x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">
    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">People</p>
</div>
<a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Users') }}</span>
</a>
@endif

@if(auth()->user()->hasAccess('resources'))
<div class="pt-4 pb-2" x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">
    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
</div>

<a href="{{ route('resources.index') }}" class="{{ request()->routeIs('resources.*') || request()->routeIs('budgets.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Resources') }}</span>
</a>

<a href="{{ route('infrastructure-costs.index') }}" class="{{ request()->routeIs('infrastructure-costs.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Infrastructure Costs') }}</span>
</a>
@endif

@if(auth()->user()->hasAccess('admin'))
<div class="pt-4 pb-2" x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">
    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
</div>
<a href="{{ route('logs.index') }}" class="{{ request()->routeIs('logs.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Logs') }}</span>
</a>

<a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? $activeClasses : $inactiveClasses }} {{ $baseClasses }}" :class="{'justify-center': !desktopSidebarOpen && '{{ $mobile }}' != '1'}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6" :class="{'mr-0': !desktopSidebarOpen && '{{ $mobile }}' != '1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <span x-show="desktopSidebarOpen || '{{ $mobile }}' == '1'">{{ __('Settings') }}</span>
</a>
@endif
