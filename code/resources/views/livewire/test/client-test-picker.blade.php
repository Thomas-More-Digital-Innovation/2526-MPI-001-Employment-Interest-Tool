<div class="text-black">
    @foreach ($tests as $test)
        <div class="flex justify-between items-center p-2 text-md bg-zinc-400/20 border-zinc-300 dark:bg-zinc-600/40 rounded-2xl border m-2 dark:border-zinc-500">
            {{-- Check if the test is assigned to the user --}}
            <flux:heading size="xl">{{ $test->test_name }}</flux:heading>
            <flux:button variant="primary" color="green" wire:click="startTest({{ $test->test_id }})" class="ease-in-out duration-200 hover:rounded-3xl px-5 py-3 text-white">
                Start
            </flux:button>
        </div>
    @endforeach
</div>

{{-- <div>
    @foreach ($tests as $test)
        <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl m-3">
            <h1 class="text-gray-900 dark:text-white text-2xl">{{$test->test_name}}</h1>
            <button
                wire:click="startTest({{ $test->test_id }})"
                class="bg-green-500 hover:bg-green-600 text-white text-xl px-6 py-3 rounded-lg duration-200">
                {{ __('Start Test') }}
            </button>
        </div>
    @endforeach
</div> --}}
