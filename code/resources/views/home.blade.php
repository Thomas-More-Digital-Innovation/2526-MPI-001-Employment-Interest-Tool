<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="">
        <div class="absolute top-4 right-4 z-50">
            <x-language-selector />
        </div>

        <section class="flex min-h-screen">
            <div class="w-1/2 flex-col min-h-screen hidden xl:flex bg-white">
                <div class="flex flex-col self-center justify-end min-h-1/2 px-8">
                    <h1 class="text-5xl pb-2 text-black">{{ __('AITscore') }}</h1>
                    <p class="text-1xl text-black">{{ __('Home page description') }}</p>
                </div>
                <div class="flex items-end justify-between p-10 min-h-1/2">
                    <img src="LogoMPI.svg" class="h-8 w-1/4" alt="MPI-Osterloo Logo">
                    <img src="TMLogo.svg" class="h-14 w-1/4" alt="Thomas More Logo">
                </div>
            </div>
            <div class="w-full xl:w-1/2 flex items-center justify-center bg-neutral-800 ">
                <div class="xl:w-1/2 bg-mpi rounded-3xl">
                    <livewire:auth.login />
                </div>
            </div>
        </section>
        <x-faq pageHeight="min-h-screen"/>
        @fluxScripts
    </body>
</html>
