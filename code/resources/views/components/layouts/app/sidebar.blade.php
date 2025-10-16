<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 {{ strtolower(auth()->user()->vision_type) ?? 'normal' }}">
{{--     color blindness svg --}}
    <svg xmlns="http://www.w3.org/2000/svg"
         width="0" height="0"
         style="position:absolute; left:-9999px; overflow:hidden;"
         aria-hidden="true" focusable="false">
        <defs>
            <filter id="protanopia">
                <feColorMatrix
                    in="SourceGraphic"
                    type="matrix"
                    values="0.567, 0.433, 0,     0, 0
                0.558, 0.442, 0,     0, 0
                0,     0.242, 0.758, 0, 0
                0,     0,     0,     1, 0"/>
            </filter>
            <filter id="deuteranopia">
                <feColorMatrix
                    in="SourceGraphic"
                    type="matrix"
                    values="0.625, 0.375, 0,   0, 0
                0.7,   0.3,   0,   0, 0
                0,     0.3,   0.7, 0, 0
                0,     0,     0,   1, 0"/>
            </filter>
            <filter id="tritanopia">
                <feColorMatrix
                    in="SourceGraphic"
                    type="matrix"
                    values="0.95, 0.05,  0,     0, 0
                0,    0.433, 0.567, 0, 0
                0,    0.475, 0.525, 0, 0
                0,    0,     0,     1, 0"/>
            </filter>
        </defs>
    </svg>
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo-small />
            </a>

            <flux:navlist variant="outline">
                @if(auth()->user()->isSuperAdmin())
                    @include('components.layouts.app.sidebar-superadmin')
                @elseif(auth()->user()->isAdmin())
                    @include('components.layouts.app.sidebar-admin')
                @elseif(auth()->user()->isMentor())
                    @include('components.layouts.app.sidebar-mentor')
                @elseif(auth()->user()->isResearcher())
                    @include('components.layouts.app.sidebar-researcher')
                @endif
            </flux:navlist>

            <flux:spacer />

            <livewire:user-profile />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <livewire:user-profile />
        </flux:header>

        {{ $slot }}

        @fluxScripts
        @livewireScripts
        @stack('scripts')
    </body>
</html>
