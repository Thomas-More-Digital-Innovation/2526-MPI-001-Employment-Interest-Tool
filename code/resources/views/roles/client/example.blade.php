<x-layouts.testlayout>
    <section>
        <div class="border dark:border-zinc-300/20 min-h-[40vh] bg-zinc-600/10 dark:bg-zinc-400/10 m-3 rounded-2xl">
            @livewire('client-test-picker')
        </div>
        <div class="min-h-[50vh] flex grow w-full">
            <div class="border dark:border-zinc-300/20 bg-zinc-600/10 dark:bg-zinc-400/10 m-3 rounded-2xl w-1/2 ">
                <flux:heading class="px-3 py-1" size="xl">Previous Attempts</flux:heading>
                @livewire('test-overview')
            </div>
            <div class="flex justify-center items-center border dark:border-zinc-300/20 bg-zinc-600/10 dark:bg-zinc-400/10 m-3 rounded-2xl w-1/2 ">
                @livewire('show-mentor')
            </div>
        </div>
    </section>
</x-layouts.testlayout>
