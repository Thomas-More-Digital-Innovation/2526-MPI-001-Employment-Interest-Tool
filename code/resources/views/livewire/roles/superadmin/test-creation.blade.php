<div class="flex flex-col md:flex-row gap-4">
    <main class="w-full md:w-3/4">
        {{-- Base form for inputting question data --}}
        <form wire:submit.prevent class="space-y-4" wire:key="editor-{{ $selectedQuestion }}">
            {{-- Name of the whole test --}}
            <flux:input wire:model.defer="test_name" placeholder="{{ __('testcreation.test_name_placeholder') }}"
                label="{{ __('testcreation.test_name_label') }}" type="text" />
            <div
                class="flex flex-col md:flex-row bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-2xl">
                {{-- Image display area --}}
                @php
                    $imgSet =
                        isset($questions[$selectedQuestion]['uploaded_image']) ||
                        (isset($questions[$selectedQuestion]['media_link']) &&
                            !empty($questions[$selectedQuestion]['media_link']));
                @endphp
                <label for="Upload-Image"
                    class="min-h-[25vh] rounded-2xl flex-1 flex items-center justify-center m-4 cursor-pointer {{ $imgSet ? '' : 'bg-zinc-300 dark:bg-zinc-600' }}">
                    <x-question-image
                        :media-link="$questions[$selectedQuestion]['media_link'] ?? ''"
                        :uploaded-image="$questions[$selectedQuestion]['uploaded_image'] ?? null"
                        alt="Question Image" />
                </label>
                <input id="Upload-Image" type="file" wire:model="questions.{{ $selectedQuestion }}.uploaded_image"
                    accept="image/*" class="hidden">
                {{-- Where the selected question is shown --}}
                <div class="flex-1 flex-col m-4 p-2">
                    <div class="mb-5">
                        {{-- Input for the title --}}
                        <flux:input class="mb-2"
                            wire:model.live.debounce.300ms="questions.{{ $selectedQuestion }}.title"
                            placeholder="{{ __('testcreation.title_placeholder') }}"
                            label="{{ __('testcreation.title_label') }}" type="text" />
                        {{-- Input for the description --}}
                        <flux:textarea class="mb-2"
                            wire:model.live.debounce.300ms="questions.{{ $selectedQuestion }}.description"
                            placeholder="{{ __('testcreation.description_placeholder') }}"
                            label="{{ __('testcreation.description_label') }}" />
                        {{-- Selection of interest field via modal --}}
                        <div class="mb-2">
                            <flux:label>{{ __('testcreation.interest_field_label') }}</flux:label>
                            <div class="flex items-center gap-2 mt-1">
                                @php
                                    $selectedInterestId = $questions[$selectedQuestion]['interest'] ?? -1;
                                    $selectedInterest = $interestFields->firstWhere(
                                        'interest_field_id',
                                        $selectedInterestId,
                                    );
                                @endphp
                                <flux:modal.trigger name="interest-field-modal">
                                    <div
                                        class="flex-1 px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 cursor-pointer hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors">
                                        @if ($selectedInterest)
                                            <span>{{ $selectedInterest->getName(app()->getLocale()) }}</span>
                                        @else
                                            <span
                                                class="text-zinc-400">{{ __('testcreation.choose_interest_field') }}</span>
                                        @endif
                                    </div>
                                </flux:modal.trigger>
                                <flux:modal.trigger name="interest-field-modal">
                                    <flux:button type="button">
                                        {{ __('actions.choose') }}
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>
                        </div>
                        {{-- audio box --}}
                        @php
                            // Retrieve the sound name (the filename stored in DB)
                            $soundName = $questions[$selectedQuestion]['sound_link'] ?? null;
                            // Generate the full URL to the sound file if the name is set exists
                            $soundUrl = $soundName ? route('question.sound', ['filename' => $soundName]) : null;
                            // Create a unique key that includes both the question index and whether it has audio
                            $recorderKey = "recorder-{$selectedQuestion}-" . ($soundUrl ? 'audio' : 'noaudio');
                        @endphp
                        <div x-data="recorder({ qid: {{ $selectedQuestion }}, existingUrl: @js($soundUrl) })" x-init="init()"
                            wire:key="{{ $recorderKey }}" wire:ignore>

                            {{-- The container for the recorder, initialises the recorder in alpine --}}
                            <div class="flex items-center mt-3 w-full gap-2 inline-flex flex-wrap">
                                <div>
                                    <!-- Record controls -->
                                    <flux:button type="button" @click="start" x-show="canRecord && !isRecording"
                                        variant="primary" color="red" icon="microphone">
                                        {{ __('testcreation.record') }}
                                    </flux:button>

                                    <flux:button type="button" @click="stop" x-show="canRecord && isRecording"
                                        variant="primary" icon="stop">
                                        {{ __('testcreation.stop') }}
                                    </flux:button>

                                    <!-- Play/Pause -->
                                    <flux:button type="button" @click="togglePlay" x-show="hasAudio && !isPlaying"
                                        variant="primary" color="blue" icon="play">
                                        {{ __('testcreation.play') }}
                                    </flux:button>

                                    <flux:button type="button" @click="togglePlay" x-show="hasAudio && isPlaying"
                                        variant="primary" color="blue" icon="pause">
                                        {{ __('testcreation.pause') }}
                                    </flux:button>

                                    <!-- Clear the sound (re-enables recording) -->
                                    <flux:button type="button" @click="clearAll" x-show="hasAudio || !canRecord"
                                        icon="trash">
                                        {{ __('testcreation.clear') }}
                                    </flux:button>
                                    <!-- Status/Error label -->
                                    {{-- <span class="text-sm text-gray-600 dark:text-gray-300 ml-3" x-text="label"></span> --}}
                                    <!-- Hidden audio element for making playing audio possible -->
                                    <audio x-ref="audio" preload="metadata"></audio>
                                </div>
                                <span> {{ __('testcreation.or') }}</span>
                                <div>
                                    <flux:button type="button" icon="speaker-wave"
                                        onclick="document.getElementById('Audio-Uploader').click()">
                                        {{ __('testcreation.choose_sound') }}
                                    </flux:button>
                                    <input id="Audio-Uploader" type="file"
                                        wire:model="questions.{{ $selectedQuestion }}.uploaded_sound"
                                        accept=".mp3,audio/mpeg,audio/wav,audio/x-wav,audio/ogg,audio/webm"
                                        class="hidden" x-on:change="label = '{{ __('testcreation.uploading') }}';"
                                        {{-- x-bind:disabled="!canRecord"> --}} />
                                    @error('questions.' . $selectedQuestion . '.uploaded_sound')
                                        <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Container for uploading media --}}

                            <div class="flex items-center mt-3 w-full gap-2 inline-flex flex-wrap md:flex-nowrap">
                                <flux:button type="button" icon="photo"
                                    onclick="document.getElementById('Upload-Image-2').click()">
                                    {{ __('testcreation.choose_image') }}
                                </flux:button>
                                <input id="Upload-Image-2" type="file"
                                    wire:model="questions.{{ $selectedQuestion }}.uploaded_image" accept="image/*"
                                    class="hidden">
                                @error('questions.' . $selectedQuestion . '.uploaded_image')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                                <span> {{ __('testcreation.or') }}</span>
                                @php
                                    $currentMediaLink = $questions[$selectedQuestion]['media_link'] ?? '';
                                    $appUrl = config('app.url');
                                    // Check if it's an external link (has http/https but NOT from our domain)
                                    $isExternalLink = (str_starts_with($currentMediaLink, 'http://') || str_starts_with($currentMediaLink, 'https://'))
                                                      && !str_starts_with($currentMediaLink, $appUrl);
                                @endphp
                                <div x-data="{
                                    mediaLink: $wire.entangle('questions.{{ $selectedQuestion }}.media_link').live,
                                    appUrl: @js($appUrl),
                                    inputValue: @js($isExternalLink ? $currentMediaLink : ''),
                                    init() {
                                        this.$watch('mediaLink', (value) => {
                                            // When mediaLink changes from Livewire, update input only if external
                                            const link = value || '';
                                            const isExt = (link.startsWith('http://') || link.startsWith('https://'))
                                                         && !link.startsWith(this.appUrl);
                                            this.inputValue = isExt ? value : '';
                                        });
                                    },
                                    updateLink() {
                                        this.mediaLink = this.inputValue;
                                    }
                                }" class="flex-1">
                                    <input
                                        type="text"
                                        x-model="inputValue"
                                        @input.debounce.300ms="updateLink()"
                                        placeholder="{{ __('testcreation.media_link_placeholder') }}"
                                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600"
                                    />
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </form>
        {{-- Container for submitting the test --}}
        <div class="my-3 flex mt-4 items-center justify-end w-full gap-4" wire:key="upload-{{ $selectedQuestion }}">
            {{-- Submit button --}}
            <div>
                <flux:button wire:click="uploadTest" variant="primary" color="rose">{{ __('Save') }}</flux:button>
            </div>
        </div>
    </main>
    {{-- Sidebar --}}
    <aside class="w-full md:w-1/4 p-3 md:self-start">
        <div
            class="flex flex-col mt-3 bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 pl-2 rounded-xl mb-4 md:max-h-[85vh]">

            {{-- Header + Button to add questions --}}
            <div class="flex w-full justify-between p-2 items-center flex-shrink-0">
                <flux:heading size="lg">{{ __('testcreation.questions') }}</flux:heading>
                <flux:button type="button" wire:click.stop="createQuestion" variant="primary" color="green">+
                </flux:button>
            </div>

            {{-- Randomize Questions Button --}}
            <div class="flex w-full p-2 items-center flex-shrink-0">
                <button
                    type="button"
                    wire:click="randomizeQuestions"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-black text-sm font-medium py-2 px-4 rounded-lg transition">
                    {{ __('testcreation.randomize_questions') }}
                </button>
            </div>

            {{-- Container to list + sort questions automatically, Basic-sortable is a selector for the sortable.js script --}}
            <ul id="Basic-sortable" class="w-full px-2 overflow-y-auto flex-1 min-h-0" x-data x-init="if (!$el._sortableBound) {
                $el._sortableBound = true;
                new Sortable($el, {
                    animation: 150,
                    dragClass: 'opacity-0',
                    onMove(e) {
                        // Update visual indexes during drag
                        requestAnimationFrame(() => {
                            const items = $el.querySelectorAll('li span');
                            items.forEach((span, idx) => {
                                const text = span.textContent;
                                span.textContent = text.replace(/\d+/, idx + 1);
                            });
                        });
                    },
                    onEnd(e) {
                        if (e.oldIndex === e.newIndex) return;
                        $wire.reorderQuestions(e.oldIndex, e.newIndex);
                    }
                });
            }">
                {{-- Accessing the array within the array --}}
                @foreach ($questions as $index => $question)
                    {{-- If selectedQuestion is $index, apply the active:bg-zinc-300 dark:active:bg-zinc-500 classes --}}
                    <li wire:key="question-{{ $index }}" wire:click="selectQuestion({{ $index }})"
                        class="cursor-grab w-full flex justify-between items-center rounded my-2 active:bg-zinc-300 dark:active:bg-zinc-500 {{ $selectedQuestion === $index ? 'bg-zinc-300 dark:bg-zinc-500' : '' }}">
                        {{-- Tally svg to indicate the bars are sortable --}}
                        <flux:icon.bars-3 class="mr-1" />
                        {{-- Icon to indicate whether question is good to be submitted or not --}}
                        <div class="flex items-center justify-center w-1/12 mr-2">
                            <div class="w-4 h-4 rounded-full"
                                style="background: {{ $question['circleFill'] ?? 'red' }}"></div>
                        </div>
                        {{-- Button that assigns the clicked question as the selected one and displays the values on the main container, text is truncated and shows up on hover as a tooltip :-) --}}
                        <span class="w-full justify-start text-left truncate whitespace-nowrap overflow-hidden">
                            @php
                                $questionInterestId = $question['interest'] ?? -1;
                                $questionInterest = $interestFields->firstWhere('interest_field_id', $questionInterestId);
                                $interestName = $questionInterest ? $questionInterest->getName(app()->getLocale()) : null;
                            @endphp
                            {{ $interestName ? __('testcreation.question_title', ['number' => $index + 1, 'title' => $interestName]) : __('testcreation.question_undefined', ['number' => $index + 1]) }}
                        </span>
                        {{-- Button + svg of a trashcan that removes the question from the array :) --}}
                        <flux:button variant="ghost" icon="trash"
                            wire:click="removeQuestion({{ $index }})" />
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    {{-- Interest Field Selection Modal --}}
    <flux:modal name="interest-field-modal" class="max-w-4xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('testcreation.choose_interest_field') }}</flux:heading>

            {{-- Search bar --}}
            <flux:input wire:model.live.debounce.200ms="interestFieldSearch"
                placeholder="{{ __('actions.search') }}..." icon="magnifying-glass" clearable>
            </flux:input>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto p-2">
                @forelse ($this->filteredInterestFields as $interestField)
                    @php
                        $isSelected =
                            ($questions[$selectedQuestion]['interest'] ?? -1) == $interestField->interest_field_id;
                    @endphp
                    <flux:modal.close>
                        <button type="button"
                            wire:click="$set('questions.{{ $selectedQuestion }}.interest', {{ $interestField->interest_field_id }})"
                            class="w-full p-4 border-2 rounded-lg text-left transition-all hover:border-blue-500 hover:shadow-lg
                                   {{ $isSelected ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-300 dark:border-zinc-600' }}">
                            <div class="font-semibold text-lg mb-1">
                                {{ $interestField->getName(app()->getLocale()) }}
                            </div>

                        </button>
                    </flux:modal.close>
                @empty
                    <div class="col-span-full text-center py-8 text-zinc-500 dark:text-zinc-400">
                        {{ __('actions.no_results_found') }}
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost">
                        {{ __('actions.close') }}
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    {{-- Incomplete Questions Error Modal --}}
    <flux:modal name="incomplete-questions-modal" class="max-w-md"
        x-data="{ incompleteQuestions: [], noComplete: false }"
        x-on:show-incomplete-questions-modal.window="
            incompleteQuestions = $event.detail.questions || [];
            noComplete = $event.detail.noComplete || false;
            $flux.modal('incomplete-questions-modal').show();
        ">
        <div>
            <flux:heading size="lg" class="mb-4">{{ __('testcreation.incomplete_questions_title') }}</flux:heading>
            
            <div class="mb-4">
                <p class="text-zinc-600 dark:text-zinc-400 mb-3" x-show="noComplete">
                    {{ __('testcreation.no_complete_questions_message') }}
                </p>
                <p class="text-zinc-600 dark:text-zinc-400 mb-3" x-show="!noComplete">
                    {{ __('testcreation.incomplete_questions_warning') }}
                </p>
                
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    <p class="text-sm font-semibold text-red-800 dark:text-red-400 mb-2">
                        {{ __('testcreation.incomplete_questions_list') }}
                    </p>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                        <template x-for="qNum in incompleteQuestions" :key="qNum">
                            <li x-text="'{{ __('testcreation.question') }} ' + qNum"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                {{-- Show only Close button if no complete questions --}}
                <template x-if="noComplete">
                    <flux:modal.close>
                        <flux:button variant="primary">
                            {{ __('actions.close') }}
                        </flux:button>
                    </flux:modal.close>
                </template>
                
                {{-- Show Cancel and Save buttons if there are complete questions --}}
                <template x-if="!noComplete">
                    <div class="flex gap-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">
                                {{ __('actions.cancel') }}
                            </flux:button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <flux:button wire:click="saveTest" variant="primary">
                                {{ __('testcreation.save_complete_questions') }}
                            </flux:button>
                        </flux:modal.close>
                    </div>
                </template>
            </div>
        </div>
    </flux:modal>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
        <script>
            function recorder(cfg) {
                return {
                    qid: cfg.qid,
                    existingUrl: cfg.existingUrl ?? null,

                    isRecording: false,
                    isPlaying: false,
                    hasAudio: false,
                    canRecord: true,

                    label: @js(__('testcreation.can_record')),


                    _stream: null,
                    _rec: null,
                    _chunks: [],

                    init() {
                        // Logic for loading existing audio if available in the DB
                        if (this.existingUrl) {
                            this._setAudio(this.existingUrl);
                            this.label = @js(__('testcreation.audio_loaded'));
                            this.hasAudio = true;
                            this.canRecord = false;
                        } else {
                            this.hasAudio = false;
                            this.canRecord = true;
                        }


                        document.addEventListener('livewire:load', () => this._wireEvents());
                        this._wireEvents();
                    },

                    _wireEvents() {
                        // Avoid binding multiple times
                        window.removeEventListener('sound-updated', this._onSoundUpdatedBound);
                        window.removeEventListener('sound-cleared', this._onSoundClearedBound);
                        // Bind the events
                        this._onSoundUpdatedBound = (e) => {
                            const {
                                index,
                                url
                            } = e.detail || {};
                            if (index === this.qid && url) {
                                this._setAudio(url);
                                this.label = @js(__('testcreation.audio_ready'));
                                this.hasAudio = true;
                                this.canRecord = false;
                            }
                        };
                        // Clear event
                        this._onSoundClearedBound = (e) => {
                            const {
                                index
                            } = e.detail || {};
                            if (index === this.qid) {
                                this._clearAudioEl();
                                this.label = @js(__('testcreation.record_cleared'));
                                this.hasAudio = false;
                                this.canRecord = true;
                            }
                        };
                        // Add the event listeners
                        window.addEventListener('sound-updated', this._onSoundUpdatedBound);
                        window.addEventListener('sound-cleared', this._onSoundClearedBound);
                    },
                    // Start recording
                    async start() {
                        if (!this.canRecord || this.isRecording) return;
                        // Different sound bytes are combined into this array/blob :)
                        this._chunks = [];
                        const stream = await navigator.mediaDevices.getUserMedia({
                            audio: true
                        });
                        this._stream = stream;
                        // Audio recording magic
                        this._rec = new MediaRecorder(stream);
                        this._rec.ondataavailable = (ev) => {
                            if (ev.data.size) this._chunks.push(ev.data);
                        };
                        this._rec.onstop = () => {
                            this._onStopRecording();
                        };
                        this._rec.start();
                        this.isRecording = true;
                        this.label = @js(__('testcreation.recording'));
                    },
                    // Stop recording
                    stop() {
                        if (!this.isRecording) return;
                        this._rec.stop();
                        this._stream.getTracks().forEach(t => t.stop());
                        this.isRecording = false;
                    },
                    // Listener, after recording is stopped, save everything in a blob and upload it
                    _onStopRecording() {
                        const blob = new Blob(this._chunks, {
                            type: 'audio/webm'
                        });

                        const localUrl = URL.createObjectURL(blob);
                        this._setAudio(localUrl);
                        this.hasAudio = true;
                        this.isPlaying = false;


                        const file = new File([blob], `rec_${Date.now()}.webm`, {
                            type: 'audio/webm'
                        });

                        this.canRecord = false;
                        this.label = @js(__('testcreation.uploading'));


                        this.$wire.upload(`questions.${this.qid}.uploaded_sound`, file,
                            () => {

                                this.label = @js(__('testcreation.uploaded'));
                            },
                            (err) => {

                                this.label = @js(__('testcreation.upload_failed'));
                                this.canRecord = true;
                                this.hasAudio = false;
                                this._clearAudioEl();
                                console.error(err);
                            }
                        );
                    },
                    // Play or pause the audio
                    togglePlay() {
                        const a = this.$refs.audio;
                        if (!a || !this.hasAudio || !a.src) return;
                        if (a.paused) {
                            a.play();
                            this.isPlaying = true;
                            a.onended = () => {
                                this.isPlaying = false;
                            };
                        } else {
                            a.pause();
                            this.isPlaying = false;
                        }
                    },
                    // Clear the audio, re-enable recording
                    async clearAll() {

                        this.label = @js(__('testcreation.clearing'));
                        await this.$wire.call('clearSound', this.qid);

                    },
                    // Set the audio element's source to the given URL (if gbiven)
                    _setAudio(url) {
                        const a = this.$refs.audio;
                        if (!a) return;
                        a.src = url;
                        a.load();
                        this.hasAudio = true;
                    },
                    // Clear the audio element's source and stop playback (edgecase i found)
                    _clearAudioEl() {
                        const a = this.$refs.audio;
                        if (!a) return;
                        a.pause();
                        a.removeAttribute('src');
                        a.load();
                        this.isPlaying = false;
                    },
                }
            }
        </script>
    @endpush
</div>
