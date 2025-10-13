<div>
    @foreach ($attempts as $attempt)
        <div class="flex justify-between items-center p-2 text-md bg-zinc-400/20 border-zinc-300 dark:bg-zinc-600/40 rounded-2xl border m-2 dark:border-zinc-500">
            <h2>{{ $attempt->test->test_name }}</h2>
            <p>{{ __('user.attempted_at') }}: {{ $attempt->created_at->format('d M Y, H:i') }} 
                @if($attempt->finished) <button disabled>Finished</button> 
                @else <button wire:click="continueTest({{ $attempt->test_attempt_id }})">Continue</button> @endif</p>
        </div>
    @endforeach
</div>
