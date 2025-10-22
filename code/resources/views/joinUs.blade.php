<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{__('InteresseTest')}}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body>
    <div class="absolute top-4 left-4 z-50">
        <flux:button href="{{ route('home') }}" >
            <flux:icon.arrow-left class="w-4 h-4"/>
            {{ __('Go Home') }}</flux:button>
    </div>
    <div class="absolute top-4 right-4 z-50 bg-neutral-800 rounded">
        <x-language-selector />
    </div>
    <section class="flex justify-center items-center h-screen">
        <livewire:join-us-form/>
    </section>
    @fluxScripts
</body>

</html>