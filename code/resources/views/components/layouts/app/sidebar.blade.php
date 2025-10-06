<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
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
                @elseif(auth()->user()->isClient())
                    @include('components.layouts.app.sidebar-client')
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
    </body>
</html>
