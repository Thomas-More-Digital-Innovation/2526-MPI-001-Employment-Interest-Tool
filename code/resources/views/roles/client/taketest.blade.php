<x-layouts.app.sidebar>
     <flux:main>
        <div class="space-y-6 p-6">
            <div class="bg-white dark:bg-gray-700 overflow-hidden shadow rounded-2xl">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Take test') }}</h2>
                    @livewire('client-test-picker')
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
