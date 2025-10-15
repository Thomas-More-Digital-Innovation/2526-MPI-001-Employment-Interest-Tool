<div>
    @foreach ($attempts as $index => $attempt)
        <div class="flex justify-between items-center p-2 text-md bg-zinc-400/20 border-zinc-300 dark:bg-zinc-600/40 rounded-2xl border m-2 dark:border-zinc-500">
            <h2>{{ $attempt->test->test_name }}</h2>
            <p>{{ __('user.attempted_at') }}:
                <x-localized-time :datetime="$attempt->created_at" />
                @if($attempt->finished) <flux:button variant="primary" :size="'lg'" wire:click="viewResults({{ $index }})">{{ __('user.view_results')}}</flux:button>
                @else <flux:button variant="primary" color="orange" :size="'xl'" wire:click="continueTest({{ $index }})">{{ __('Continue')}}</flux:button> @endif</p>
        </div>
    @endforeach
</div>
