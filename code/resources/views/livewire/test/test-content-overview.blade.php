<article>
    <div class="space-y-6 pb-6 sticky top-0 z-20">
        <div class="bg-white shadow rounded-lg  border-b border-gray-200">
            <div class="px-4 py-5 sm:px-6 flex gap-2 items-center">
                <flux:button icon="arrow-left" wire:click="close" class="shrink-0"></flux:button>

                <h2 class="text-2xl font-bold tracking-tight text-gray-900">{{ $testName }}</h2>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 justify-center">
        @forelse ($testContent as $questionNumber => $question)
        <div class="w-80 rounded-2xl bg-zinc-600">
            <img src="{{ $question->getImageUrl($currentLocale) }}" class="w-full h-64 object-cover rounded-t-2xl" alt="{{ $question->getImageDescription($currentLocale) }}">
            <div class="p-2">
                <div class="flex justify-between">
                    <p class="font-bold">{{ $questionNumber }}.</p>
                    @php
                    $audio = $question->getSoundLink($currentLocale);
                    @endphp
                    @if ($audio)
                    <audio x-ref="questionaudio{{$questionNumber}}" src="{{ $audio }}"></audio>
                    <button class="text-2xl @if(!$audio) invisible @endif"
                        @click="
                        let audio = $audio;
                        audio.pause();
                        audio.currentTime = 0;
                        audio.load();
                        audio.play();
                        ">
                        <flux:icon.speaker-wave class="size-6" />
                    </button>
                    @endif
                </div>
                <h2 class="text-xls">{{ $question->getQuestion($currentLocale) }}</h2>

            </div>
        </div>

        @empty
        <p>{{ __('No questions found') }}</p>
        @endforelse
    </div>
</article>