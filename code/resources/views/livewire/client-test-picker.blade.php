<div class="text-black">
    @foreach ($tests as $test)
        <div class="flex justify-between items-center p-4 text-lg bg-gray-100 rounded-2xl border m-2">
            {{-- Check if the test is assigned to the user --}}
            <h1 class="text-black text-2xl">{{$test->test_name}}</h1>
            <button wire:click="startTest({{ $test->test_id }})" class="bg-green-400 text-lg rounded-2xl px-5 py-3 text-white">
                Start
            </button>
        </div>
    @endforeach
</div>
