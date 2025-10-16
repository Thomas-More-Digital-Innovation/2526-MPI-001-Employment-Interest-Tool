<div>
    <flux:heading size="xl" class="pb-5">{{ __('testcreation.test_manager') }}</flux:heading>
    <div class="border rounded-2xl pt-5 scroll-m-5 p-3 border-zinc-400 dark:border-zinc-500 bg-zinc-300/40 dark:bg-zinc-700/70">
        @foreach($tests as $test)
            <div class="border bg-zinc-300/70 dark:bg-zinc-600 border-zinc-400 dark:border-zinc-400 m-2 p-2 rounded-2xl items-center flex justify-between">
                <flux:heading size="xl">{{$test->test_name}}</flux:heading>
                <div>
                    <flux:button wire:click="deleteTest({{ $test->test_id }})" color="red" variant="primary" icon="trash">{{ __('actions.delete') }}</flux:button>
                    <flux:button wire:click="loadTest({{ $test->test_id }})" color="slate" variant="primary" icon="pencil" icon:variant="outline">{{ __('actions.edit') }}</flux:button>
                </div>
            </div>
        @endforeach
    </div>
</div>
