<div>
    @foreach($tests as $test)
        <div class="border bg-zinc-700/40 m-2 p-2 rounded-2xl items-center flex justify-between">
            <flux:heading size="xl">{{$test->test_name}}</flux:heading>
            <flux:button wire:click="loadTest({{ $test->test_id }})" variant="primary" color="zinc">Edit</flux:button>
        </div>
    @endforeach
</div>
