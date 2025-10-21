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
        @fluxAppearance()
    </head>
    <body class="light">
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
                            <img src="LogoMPI.svg" class="h-8 w-1/4" alt="MPI-Osterloo Logo">
                            <img src="TMLogo.svg" class="h-14 w-1/4" alt="Thomas More Logo">
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 flex items-center justify-center bg-accent-content ">
                        <div class="md:w-1/2 bg-mpi rounded-3xl">
                            <livewire:auth.login />
                            <div x-data="{ showModal: false }">
                                <!-- Modal -->
                                <div x-show="showModal" 
                                    class="fixed inset-0 bg-black/40 bg-opacity-50 z-50 flex items-center justify-center"
                                    x-transition>
                                    <div class="bg-white p-6 rounded-lg shadow-xl max-w-md">
                                        <h3 class="text-lg font-bold mb-4">{{ __('Confirm') }}</h3>
                                        <p class="mb-4">{{ __('Are you sure you want to join us as an organization?') }}</p>
                                        <div class="flex justify-end space-x-3">
                                            <button @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                                {{ __('Cancel') }}
                                            </button>
                                            <a href="{{ route('organisation.joinUs') }}" class="px-4 py-2 bg-mpi text-white rounded hover:bg-opacity-90">
                                                {{ __('Continue') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Trigger Button -->
                                <button @click="showModal = true" 
                                        class="text-white duration-100 ease-in-out hover:underline hover:scale-105">
                                    {{__("Want to join us?")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            @endauth
        @endif
        <x-faq pageHeight="min-h-screen"/>
        @fluxScripts()
    </body>
</html>
