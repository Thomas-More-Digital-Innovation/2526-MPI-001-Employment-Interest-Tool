<div>
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
</div>
