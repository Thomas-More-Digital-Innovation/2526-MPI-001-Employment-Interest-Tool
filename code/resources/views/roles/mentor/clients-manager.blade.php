<x-layouts.app.sidebar>
     <flux:main>
        <div class="space-y-6 p-6">
            <div class="bg-white dark:bg-gray-700 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Clients Manager') }}</h2>
                    @livewire('mentor.clients-manager')
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>