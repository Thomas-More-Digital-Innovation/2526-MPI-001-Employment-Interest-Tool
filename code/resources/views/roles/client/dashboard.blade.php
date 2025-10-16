<x-layouts.client>
    <section class="min-h-screen flex flex-col gap-3 p-3">
        <div class="bg-zinc-600/10 dark:bg-zinc-400/10 border dark:border-zinc-300/20 rounded-2xl">
            @livewire('test.client-test-picker')
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
            <div class="bg-zinc-600/10 dark:bg-zinc-400/10 border dark:border-zinc-300/20 rounded-2xl flex flex-col">
                <flux:heading class="px-3 py-2" size="lg md:xl">
                    {{ __('user.previous_attempts') }}
                </flux:heading>
                <div class="flex-1 px-3 pb-3">
                    @livewire('test.test-result-overview')
                </div>
            </div>
            <div class="bg-zinc-600/10 dark:bg-zinc-400/10 border dark:border-zinc-300/20 rounded-2xl flex items-center justify-center p-3">
                @livewire('show-mentor')
            </div>
        </div>
    </section>
</x-layouts.client>
