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
            <div class="col-span-2 md:col-span-1 row-start-1 md:order-2 flex flex-col content-center ">
                <img src="{{ $image }}" alt="{{ $title }}" class="object-cover rounded-md">
                <h2 class="text-4xl md:text-5xl font-semibold mt-2 text-center">{{ $title }}</h2>
            </div>

            <!-- Buttons -->
            <div class="h-full row-start-2 md:row-start-1 md:order-1">
                <button class="w-full h-full bg-green-400 rounded-md"
                        wire:click="like"></button>
            </div>
            <div class="h-full row-start-2 md:row-start-1 md:order-3">
                <button class="w-full h-full bg-red-500 rounded-md"
                        wire:click="dislike"></button>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex justify-around items-center m-4">
            <button wire:click="previous" class="text-6xl"><flux:icon.arrow-left class="size-12 md:size-32" /></button>
            <button class="text-2xl" @click="$refs.ouch.play()"><flux:icon.speaker-wave class="size-8 md:size-24" /></button>
            <audio x-ref="ouch">
                <source src="{{ $audio }}" type="audio/mpeg" />
            </audio>
            <button class="text-2xl">?</button>
            <button wire:click="next" class="text-6xl"><flux:icon.arrow-right class="size-12 md:size-32" /></button>
        </div>

        <!-- Progress bar -->
        <div class="w-full bg-gray-400">
            <div class="bg-red-400 py-2 w-{{$questionNumber}}/{{$totalQuestions}}"></div>
        </div>
    </div>
