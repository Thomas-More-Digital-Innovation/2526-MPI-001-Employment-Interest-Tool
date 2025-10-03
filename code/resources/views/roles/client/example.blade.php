<x-layouts.app.sidebar>
     <flux:main>
        <div class="space-y-6 p-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold tracking-tight text-black">{{ __('Client example page') }}</h2>
                    @livewire('client-test-picker')
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
