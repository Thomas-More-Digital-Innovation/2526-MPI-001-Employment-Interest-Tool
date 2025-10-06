<div class="text-black">
    @foreach ($tests as $test)
        <div class="flex justify-between items-center p-4 text-lg bg-zinc-400/40 border-zinc-300 dark:bg-zinc-600/40 rounded-2xl border m-2 dark:border-zinc-500">
            {{-- Check if the test is assigned to the user --}}
            <flux:heading size="xl">{{ $test->test_name }}</flux:heading>
            <flux:button wire:click="startTest({{ $test->test_id }})" class=" rounded-2xl ease-in-out duration-200 hover:rounded-3xl px-5 py-3 text-white">
                Start
            </flux:button>
        </div>
    @endforeach
</div>
