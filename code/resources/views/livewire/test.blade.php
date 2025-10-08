<div class="flex flex-col h-full">
    <!-- Close button -->
    <div class="hidden md:flex justify-end ">
        <button class="p-2 rounded text-xl bg-red-600"
                wire:click="close">
            <flux:icon.x-mark />
        </button>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-2 md:grid-cols-3 grid-rows-[auto_1fr] md:grid-rows-1 gap-2 h-full mt-2">
        <!-- Image + Title -->
        <div class="col-span-2 md:col-span-1 row-start-1 md:order-2 flex flex-col content-center relative overflow-hidden"
                wire:key="image-container-{{ $questionNumber }}">
            <div class="absolute inset-0 flex items-center justify-center z-10 bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80 backdrop-blur-sm rounded-md"
                    x-data="{ show: {{ $isImageLoading ? 'true' : 'false' }} }"
                    x-show="show"
                    @img-loading-state-changed.window="show = $event.detail.loading"
                    x-transition>
                <div role="status">
                    <svg aria-hidden="true" class="w-16 h-16 text-gray-200 animate-spin dark:text-gray-600 fill-red-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <img
                src="{{ $image }}"
                alt="{{ $imageDescription ?? '' }}"
                class="object-cover rounded-md"/>
            <h2 class="text-4xl md:text-5xl font-semibold mt-2 text-center break-words overflow-hidden max-w-full">{{ $title }}</h2>
        </div>

        <!-- Buttons -->
        <div class="h-full row-start-2 md:row-start-1 md:order-1 relative">
            <button class="w-full h-full bg-green-400 rounded-md"
                    wire:click="like"
                    wire:loading.attr="disabled"
                    wire:target="like, dislike, next, previous"
                    @if($isQuestionLoading) disabled class="w-full h-full bg-green-400 rounded-md opacity-50 cursor-not-allowed" @endif>
            </button>
            <!-- Overlay for disabled state -->
            @if($isQuestionLoading)
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-md">
                    <div class="text-white text-center">
                        <div class="animate-pulse">
                            <flux:icon.clock class="size-12 mx-auto" />
                            <span class="block text-sm">Please wait</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="h-full row-start-2 md:row-start-1 md:order-3 relative">
            <button class="w-full h-full bg-red-500 rounded-md"
                    wire:click="dislike"
                    wire:loading.attr="disabled"
                    wire:target="like, dislike, next, previous"
                    @if($isQuestionLoading) disabled class="w-full h-full bg-red-500 rounded-md opacity-50 cursor-not-allowed" @endif>
            </button>
            <!-- Overlay for disabled state -->
            @if($isQuestionLoading)
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-md">
                    <div class="text-white text-center">
                        <div class="animate-pulse">
                            <flux:icon.clock class="size-12 mx-auto" />
                            <span class="block text-sm">Please wait</span>
                        </div>
                    </div>
                </div>
            @endif
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
