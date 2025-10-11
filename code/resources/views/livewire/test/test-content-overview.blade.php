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