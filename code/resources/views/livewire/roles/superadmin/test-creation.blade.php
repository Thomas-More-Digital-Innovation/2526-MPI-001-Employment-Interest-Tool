<div class="flex flex-col md:flex-row gap-4">
    <main class="w-full md:w-3/4">
        {{-- Base form for inputting question data --}}
        <form wire:submit.prevent class="space-y-4" wire:key="editor-{{ $selectedQuestion }}">
            {{-- Name of the whole test --}}
            <flux:input wire:model.defer="test_name" placeholder="{{ __('testcreation.test_name_placeholder') }}" label="{{ __('testcreation.test_name_label') }}" type="text" />
            <div class="flex flex-col md:flex-row bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-2xl">
                {{-- Image display area --}}
                <div class="bg-zinc-300 min-h-[25vh] dark:bg-zinc-600 rounded-2xl flex-1 flex items-center justify-center m-4">
                    {{-- If an image has been uploaded, show a preview of the uploaded image --}}
                    {{-- Else if an image exists in the database, show that image --}}
                    {{-- Else show a placeholder text --}}
                    @if (isset($questions[$selectedQuestion]['uploaded_image']))
                        {{-- Check if the uploaded_image is a temporary uploaded file --}}
                        {{-- If so, use temporaryUrl() to show the preview --}}
                        {{-- Else, show a loading text --}}
                        @if (is_object($questions[$selectedQuestion]['uploaded_image']) && method_exists($questions[$selectedQuestion]['uploaded_image'], 'temporaryUrl'))
                            <img src="{{ $questions[$selectedQuestion]['uploaded_image']->temporaryUrl() }}"
                                 alt="Image Preview"
                                 class="rounded-2xl max-h-full max-w-full object-contain">
                        @else
                            <span class="text-blue-500">Preview loading...</span>
                        @endif
                    @elseif (isset($questions[$selectedQuestion]['media_link']) && !empty($questions[$selectedQuestion]['media_link']))
                        <img src="{{ route('question.image', ['filename' => $questions[$selectedQuestion]['media_link']]) }}"
                             alt="Question Image"
                             class="rounded-2xl max-h-full max-w-full object-contain">
                    @else
                        <span class="text-zinc-500 dark:text-zinc-400">{{ __('testcreation.no_image_uploaded') }}</span>
                    @endif
                </div>
                {{-- Where the selected question is shown --}}
                <div class="flex-1 flex-col m-4 p-2">
                    <div class="mb-5">
                        {{-- Input for the title --}}
                        <flux:input
                            class="mb-2"
                            wire:model.live.debounce.50ms="questions.{{ $selectedQuestion }}.title"
                            placeholder="{{ __('testcreation.title_placeholder') }}"
                            :loading="false"
                            label="{{ __('testcreation.title_label') }}"
                            type="text"
                        />
                        {{-- Input for the description --}}
                        <flux:textarea
                            class="mb-2"
                            wire:model.live.debounce.300ms="questions.{{ $selectedQuestion }}.description"
                            placeholder="{{ __('testcreation.description_placeholder') }}"
                            label="{{ __('testcreation.description_label') }}"
                        />
                        {{-- Selection of interest field --}}
                        <flux:select
                            label="{{ __('testcreation.interest_field_label') }}"
                            wire:model.live="questions.{{ $selectedQuestion }}.interest"
                        >
                            <flux:select.option value="-1">{{ __('testcreation.choose_interest_field') }}</flux:select.option>
                            @foreach ($interestFields as $interestField)
                                <flux:select.option value="{{ $interestField->interest_field_id }}">{{ $interestField->getName(app()->getLocale()) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        {{-- audio box--}}
                        @php
                            // Retrieve the sound name (the filename stored in DB)
                            $soundName = $questions[$selectedQuestion]['sound_link'] ?? null;
                            // Generate the full URL to the sound file if the name is set exists
                            $soundUrl  = $soundName ? route('question.sound', ['filename' => $soundName]) : null;
                        @endphp
                        {{-- The container for the recorder, initialises the recorder in alpine --}}
                        <div
                            x-data="recorder({ qid: {{ $selectedQuestion }}, existingUrl: @js($soundUrl) })"
                            x-init="init()"
                            wire:key="recorder-{{ $selectedQuestion }}"
                            wire:ignore
                            class="flex items-center gap-3"
                        >
                            <!-- Record controls -->
                            <button @click="start" x-show="canRecord && !isRecording" class="px-3 py-2 rounded bg-red-600 text-white">● {{ __('testcreation.record') }}</button>
                            <button @click="stop" x-show="canRecord && isRecording" class="px-3 py-2 rounded bg-gray-800 text-white">{{ __('testcreation.stop') }}</button>

                            <!-- Play/Pause -->
                            <button @click="togglePlay" x-show="hasAudio" class="px-3 py-2 rounded bg-blue-600 text-white">
                                <span x-text="isPlaying ? '{{ __('testcreation.pause') }}' : '{{ __('testcreation.play') }}'"></span>
                            </button>

                            <!-- Clear the sound (re-enables recording) -->
                            <button @click="clearAll" x-show="hasAudio || !canRecord" class="px-3 py-2 rounded bg-gray-200">{{ __('testcreation.clear') }}</button>
                            <!-- Status/Error label -->
                            <span class="text-sm text-gray-600" x-text="label"></span>
                            <!-- Hidden audio element for making playing audio possible -->
                            <audio x-ref="audio" preload="metadata"></audio>
                        </div>
                        {{-- Container for uploading audio --}}
                        <div class="mt-2">
                            <input
                                type="file"
                                wire:model="questions.{{ $selectedQuestion }}.uploaded_sound"
                                accept=".mp3,audio/mpeg,audio/wav,audio/x-wav,audio/ogg,audio/webm"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                x-on:change="label = '{{ __('testcreation.uploading') }}';"
                                x-bind:disabled="!canRecord"
                            >
                            @error('questions.'.$selectedQuestion.'.uploaded_sound')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>
        </form>
        {{-- Container for uploading images --}}
        <div class="my-3 flex mt-4 items-center justify-between w-full gap-4" wire:key="upload-{{ $selectedQuestion }}">
            <div class="flex-1">
                <input type="file"
                   wire:model="questions.{{ $selectedQuestion }}.uploaded_image"
                   accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('questions.'.$selectedQuestion.'.uploaded_image')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            {{-- Submit button --}}
            <div>
                <flux:button wire:click="uploadTest" variant="primary" color="rose">{{ __('testcreation.submit') }}</flux:button>
            </div>
        </div>
    </main>
    {{-- Sidebar --}}
    <aside class="w-full md:w-1/4 p-3">
        <div class="flex-col mt-3 bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 pl-2 rounded-xl flex items-center justify-between mb-4">
            {{-- Header + Button to add questions --}}
            <div class="flex w-full justify-between p-2 items-center">
                <flux:heading size="lg">{{ __('testcreation.questions') }}</flux:heading>
                <flux:button type="button" wire:click.stop="createQuestion" variant="primary" color="green">+</flux:button>
            </div>
            {{-- Container to list + sort questions automatically, Basic-sortable is a selector for the sortable.js script --}}
            <ul id="Basic-sortable" class="w-full px-2"
                x-data
                x-init="
                if (!$el._sortableBound) {
                    $el._sortableBound = true;
                    new Sortable($el, {
                    animation: 150,
                    onEnd(e) {
                        if (e.oldIndex === e.newIndex) return;
                        $wire.reorderQuestions(e.oldIndex, e.newIndex);
                    }
                    });
                }
                ">
                {{-- Accessing the array within the array --}}
                @foreach ($questions as $index => $question)
                    <li wire:key="question-{{ $index }}" class="cursor-grab w-full flex justify-between items-center bg-zinc-600 rounded my-2">
                        {{-- Tally svg to indicate the bars are sortable --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 lucide lucide-tally2-icon lucide-tally-2"><path d="M4 4v16"/><path d="M9 4v16"/></svg>
                        {{-- Icon to indicate whether question is good to be submitted or not --}}
                        <div class="flex items-center justify-center w-1/12">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14">
                                <circle r="6" cx="7" cy="7" fill="{{ $question['circleFill'] ?? 'red' }}" />
                            </svg>
                        </div>
                        {{-- Button that assigns the clicked question as the selected one and displays the values on the main container, text is truncated and shows up on hover as a tooltip :-) --}}
                        <flux:button variant="ghost" class="w-full justify-start text-left truncate whitespace-nowrap overflow-hidden" wire:click="selectQuestion({{ $index }})" title="{{ $question['title'] ?? __('testcreation.untitled') }}">
                            {{ $question['title'] ? __('testcreation.question_title', ['number' => $index + 1, 'title' => $question['title']]) : __('testcreation.question_undefined', ['number' => $index + 1]) }}
                        </flux:button>
                        {{-- Button + svg of a trashcan that removes the question from the array :) --}}
                        <flux:button variant="ghost" wire:click="removeQuestion({{ $index }})">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 lucide lucide-trash2-icon lucide-trash-2"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </flux:button>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>
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

                    label: 'Can record, no sound file added',

                    
                    _stream: null,
                    _rec: null,
                    _chunks: [],

                    init() {
                        // Logic for loading existing audio if available in the DB
                        if (this.existingUrl) {
                            this._setAudio(this.existingUrl);
                            this.label = 'Audio loaded';
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
                            const { index, url } = e.detail || {};
                            if (index === this.qid && url) {
                                this._setAudio(url);
                                this.label = 'Audio ready';
                                this.hasAudio = true;
                                this.canRecord = false;
                            }
                        };
                        // Clear event
                        this._onSoundClearedBound = (e) => {
                            const { index } = e.detail || {};
                            if (index === this.qid) {
                                this._clearAudioEl();
                                this.label = 'Cleared. You can record or browse again';
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
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this._stream = stream;
                        // Audio recording magic
                        this._rec = new MediaRecorder(stream);
                        this._rec.ondataavailable = (ev) => { if (ev.data.size) this._chunks.push(ev.data); };
                        this._rec.onstop = () => { this._onStopRecording(); };
                        this._rec.start();
                        this.isRecording = true;
                        this.label = 'Recording..';
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
                        const blob = new Blob(this._chunks, { type: 'audio/webm' });
                        
                        const localUrl = URL.createObjectURL(blob);
                        this._setAudio(localUrl);
                        this.hasAudio = true;
                        this.isPlaying = false;

                        
                        const file = new File([blob], `rec_${Date.now()}.webm`, { type: 'audio/webm' });
                        
                        this.canRecord = false;
                        this.label = 'Uploading...';

                        
                        this.$wire.upload(`questions.${this.qid}.uploaded_sound`, file,
                            () => {
                                
                                this.label = 'Uploaded';
                            },
                            (err) => {
                                
                                this.label = 'Upload failed';
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
                            a.onended = () => { this.isPlaying = false; };
                        } else {
                            a.pause();
                            this.isPlaying = false;
                        }
                    },
                    // Clear the audio, re-enable recording
                    async clearAll() {
                        
                        this.label = 'Clearing...';
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
