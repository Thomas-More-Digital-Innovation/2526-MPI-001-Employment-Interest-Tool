
    <div class="flex flex-col h-full">

        <!-- Main content -->
        <div class="grid grid-cols-2 md:grid-cols-4 grid-rows-[auto_1fr] md:grid-rows-1 gap-2 h-full mt-2">
            <!-- Image + Title -->
            <div class="col-span-2 md:col-span-2 row-start-1 md:order-2 flex flex-col content-center ">
                <img src="{{ $image }}" alt="{{ $imageDescription }}" class="object-cover rounded-md">
            <h2 class="text-4xl md:text-5xl font-semibold mt-2 text-center break-words overflow-hidden max-w-full h-full">{{ $title }}</h2>
            </div>

            <!-- Buttons -->
            <div class="h-full row-start-2 md:row-start-1 md:order-1">
                <div class="hidden md:flex justify-start h-1/5"></div>
                <button class="w-full h-4/5 bg-green-400 rounded-full shadow-lg hover:-translate-y-2 active:translate-y-1 active:shadow-inner transition-all duration-150 cursor-pointer"
                    wire:click="like"
                    wire:loading.attr="disabled"
                    wire:target="like, dislike, next, previous"
                    @if($isQuestionLoading) disabled @endif>
                </button>
            </div>
            <div class="h-full row-start-2 md:row-start-1 md:order-3">
                <!-- Close button -->
                <div class="hidden md:flex justify-end h-1/5">
                    <button class="p-2 rounded text-xl"
                            wire:click="close">
                        <img src="/assets/stop.svg" alt="Close" class="h-full">
                    </button>
                </div>
                <button class="w-full h-4/5 bg-red-500 rounded-full shadow-lg hover:-translate-y-2 active:translate-y-1 active:shadow-inner transition-all duration-150 cursor-pointer"
                    wire:click="dislike"
                    wire:loading.attr="disabled"
                    wire:target="like, dislike, next, previous"
                    @if($isQuestionLoading) disabled @endif>
                </button>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex justify-around items-center m-4">
            <button wire:click="previous" class="text-6xl @if(!$previousEnabled) invisible @endif"><flux:icon.arrow-left class="size-12 md:size-32" /></button>

            <button class="text-2xl @if(!$audio) invisible @endif"
                    @click="
        let audio = $refs.questionaudio{{$questionNumber}};
        audio.pause();
        audio.currentTime = 0;
        audio.load();
        audio.play();
    ">
                <flux:icon.speaker-wave class="size-8 md:size-24" />
            </button>
            @if ($audio)
                <audio x-ref="questionaudio{{$questionNumber}}">
                    <source src="{{ $audio }}" type="audio/mpeg" />
                </audio>
            @endif

            <livewire:test.send-feedback-test
                    :class="'size-6 md:size-16'"
                    :clientName="$clientName"
                    :questionNumber="$questionNumber"
                    :test="$testName"
                    :mail-mentor="$mailMentor"
                    :website="route('dashboard')"
                    :onCloseEvent=" App\Livewire\Test\Test::UNCLEAR_CLOSED_EVENT "
            />

            <button wire:click="next" class="text-6xl"><flux:icon.arrow-right class="size-12 md:size-32" /></button>
        </div>
        <!-- Progress bar -->
        {{-- wire:key is needed to force the alpine component to re-render --}}
        <div  wire:key="progress-{{ $questionNumber }}"  x-data="{ progress: {{($questionNumber - 1) / $totalQuestions}} * 100 }" class="w-full bg-gray-400">
            <div class="bg-red-400 py-2" :style="`width: ${progress}%`"></div>
        </div>
    </div>

    <!-- Controls -->
    <div class="flex justify-around items-center m-4">
        <button wire:click="previous"
                class="text-6xl {{ !$previousEnabled ? 'invisible' : '' }} transition-opacity"
                wire:loading.attr="disabled"
                wire:target="like, dislike, next, previous"
                @if($isQuestionLoading) disabled class="text-6xl {{ !$previousEnabled ? 'invisible' : '' }} opacity-50 cursor-not-allowed" @endif>
            <flux:icon.arrow-left class="size-12 md:size-32" />
        </button>

        <button class="text-2xl @if(!$audio) invisible @endif"
                @click="
                    let audio = $refs.questionaudio{{$questionNumber}};
                    audio.pause();
                    audio.currentTime = 0;
                    audio.load();
                    audio.play();
                ">
            <flux:icon.speaker-wave class="size-8 md:size-24" />
        </button>
        @if ($audio)
            <audio x-ref="questionaudio{{$questionNumber}}">
                <source src="{{ $audio }}" type="audio/mpeg" />
            </audio>
        @endif

        <livewire:send-feedback-test
                :class="'size-6 md:size-16'"
                :clientName="$clientName"
                :questionNumber="$questionNumber"
                :test="$testName"
                :mail-mentor="$mailMentor"
                :website="route('dashboard')"
                :onCloseEvent=" App\Livewire\Test::UNCLEAR_CLOSED_EVENT "
        />

        <button wire:click="next"
                class="text-6xl transition-opacity"
                wire:loading.attr="disabled"
                wire:target="like, dislike, next, previous"
                @if($isQuestionLoading) disabled class="text-6xl opacity-50 cursor-not-allowed" @endif>
            <flux:icon.arrow-right class="size-12 md:size-32" />
        </button>
    </div>
    <!-- Progress bar -->
    {{-- wire:key is needed to force the alpine component to re-render --}}
    <div wire:key="progress-{{ $questionNumber }}" x-data="{ progress: {{($questionNumber - 1) / $totalQuestions}} * 100 }" class="w-full bg-gray-400">
        <div class="bg-red-400 py-2" :style="`width: ${progress}%`"></div>
    </div>
</div>
