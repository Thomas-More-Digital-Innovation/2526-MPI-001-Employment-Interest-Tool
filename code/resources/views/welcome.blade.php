<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="">
        {{-- make proper header --}}
            {{-- login register buttons, do not touch, even if you do then re-make the css as first one was BS --}}
            {{-- @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif --}}
        {{-- better if you repurpose this page so that login and register buttons are removed, replace it with the login form we have in /resources/viws/livewire/auth/login.blade.php --}}
        @if (Route::has('login'))
            @auth
            @else
                <section class="flex min-h-screen">
                    <div class="w-1/2 flex-col min-h-screen hidden md:flex">
                        <div class="flex flex-col self-center justify-end min-h-1/2 px-8">
                            <h1 class="text-5xl pb-2">AITscore</h1>
                            <p class="text-1xl">The Labor Interest Test (AIT) was developed by Thomas More Kempen. It is a simple online, visual test that maps my preferences for different work areas. By rating photos of various types of work as positive, negative, or neutral, the AIT shows which work areas best match my interests.</p>
                        </div>
                        <div class="flex items-end justify-between p-10 min-h-1/2">
                            <img src="LogoMPI.svg" class="h-8" alt="MPI-Osterloo Logo">
                            <img src="TMLogo.svg" class="h-14" alt="Thomas More Logo">
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 flex items-center justify-center bg-accent-content ">
                        <div class="md:w-1/2 bg-mpi p-9 rounded-3xl">
                            <livewire:auth.login />
                        </div>
                    </div>
                </section>
            @endauth
        @endif
    </body>
</html>
