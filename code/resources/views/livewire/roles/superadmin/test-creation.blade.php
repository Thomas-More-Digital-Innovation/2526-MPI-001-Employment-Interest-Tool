<div class="flex flex-col md:flex-row gap-4">
    <main class="w-full md:w-3/4">
        {{-- Base form for inputting question data --}}
        <form wire:submit.prevent class="space-y-4" wire:key="editor-{{ $selectedQuestion }}">
            {{-- Name of the whole test --}}
            <flux:input wire:model.defer="test_name" placeholder="Test Name" label="Test Name" type="text" />
            <div class="flex flex-col md:flex-row bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-2xl">
                {{-- Image display area --}}
                <div class="bg-zinc-300 min-h-[25vh] dark:bg-zinc-600 rounded-2xl flex-1 flex items-center justify-center m-4">
                    @if (isset($questions[$selectedQuestion]['uploaded_image']))
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
                        <span class="text-zinc-500 dark:text-zinc-400">No image uploaded</span>
                    @endif
                </div>
                {{-- Where the selected question is shown --}}
                <div class="flex-1 flex-col m-4 p-2">
                    <div class="mb-5">
                        {{-- Input for the title --}}
                        <flux:input
                            class="mb-2"
                            wire:model.live.debounce.50ms="questions.{{ $selectedQuestion }}.title"
                            placeholder="Title"
                            :loading="false"
                            label="Title"
                            type="text"
                        />
                        {{-- Input for the description --}}
                        <flux:textarea
                            class="mb-2"
                            wire:model.live.debounce.300ms="questions.{{ $selectedQuestion }}.description"
                            placeholder="Description"
                            label="Image Description"
                        />
                        {{-- Selection of interest field --}}
                        <flux:select

                            label="Interest Field"
                            wire:model.live="questions.{{ $selectedQuestion }}.interest"
                        >
                            <flux:select.option value="-1">Choose interest field...</flux:select.option>
                            @foreach ($interestFields as $interestField)
                                <flux:select.option value="{{ $interestField->interest_field_id }}">{{ $interestField->getName(app()->getLocale()) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        {{-- AUDIO BOX, tied to the currently selected question --}}
                        @php
                            $soundName = $questions[$selectedQuestion]['sound_link'] ?? null;
                            $soundUrl  = $soundName ? route('question.sound', ['filename' => $soundName]) : null;
                        @endphp


                        {{-- AUDIO BOX, tied to the currently selected question --}}
                        <div
                            x-data="recorder({ qid: {{ $selectedQuestion }}, existingUrl: @js($soundUrl) })"
                            x-init="init()"
                            wire:key="recorder-{{ $selectedQuestion }}"
                            wire:ignore
                            class="flex items-center gap-3"
                        >
                            <button @click="start" x-show="!isRecording" class="px-3 py-2 rounded bg-red-600 text-white">● Record</button>
                            <button @click="stop"  x-show="isRecording"  class="px-3 py-2 rounded bg-gray-800 text-white">Stop</button>

                            <button @click="togglePlay" x-show="hasAudio" class="px-3 py-2 rounded bg-blue-600 text-white">
                                <span x-text="isPlaying ? 'Pause' : 'Play'"></span>
                            </button>

                            <button @click="clearClient" x-show="hasAudio" class="px-3 py-2 rounded bg-gray-200">Clear</button>

                            <span class="text-sm text-gray-600" x-text="label"></span>

                            <audio x-ref="audio"></audio>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
            <div>
                <flux:button wire:click="uploadTest" variant="primary" color="rose">Submit</flux:button>
            </div>
        </div>
    </main>
    {{-- Sidebar --}}
    <aside class="w-full md:w-1/4 p-3">
        <div class="flex-col mt-3 bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 pl-2 rounded-xl flex items-center justify-between mb-4">
            {{-- Header + Button to add questions --}}
            <div class="flex w-full justify-between p-2 items-center">
                <flux:heading size="lg">Questions</flux:heading>
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
                        <flux:button variant="ghost" class="w-full justify-start text-left truncate whitespace-nowrap overflow-hidden" wire:click="selectQuestion({{ $index }})" title="{{ $question['title'] ?? 'Untitled' }}">{{ $question['title'] ? 'Question ' . ($index + 1) . ' - ' . $question['title'] : 'Question ' . ($index + 1) . ' - Undefined' }}</flux:button>
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
                    label: 'Can record, no sound file added',
                    hasAudio: false,

                    _stream: null,
                    _recorder: null,
                    _chunks: [],
                    _segments: [],
                    _currentUrl: null,
                    _loadedExisting: false,

                    init() {
                        document.addEventListener('livewire:message.processed', () => this._rebuild());
                        this._loadExistingIfAny().then(() => this._rebuild());
                    },

                    async _loadExistingIfAny() {
                        if (!this.existingUrl || this._loadedExisting) return;
                        try {
                            const res = await fetch(this.existingUrl, { cache: 'no-store' });
                            if (!res.ok) return;
                            const blob = await res.blob();
                            if (blob.size > 0) {
                                this._segments = [blob]; // seed with server audio
                                this._loadedExisting = true;
                                this.label = 'Existing audio loaded, you can record to add more';
                                this.hasAudio = true;
                            }
                        } catch {
                            // ignore fetch issues, user can still record fresh audio
                        }
                    },

                    async _ensureMic() {
                        if (!this._stream) {
                            this._stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        }
                    },

                    async start() {
                        if (this.isRecording) return;
                        await this._ensureMic();

                        this._chunks = [];
                        const options = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                            ? { mimeType: 'audio/webm;codecs=opus' }
                            : undefined;

                        this._recorder = new MediaRecorder(this._stream, options);
                        this._recorder.ondataavailable = e => { if (e.data?.size) this._chunks.push(e.data); };
                        this._recorder.onstop = () => {
                            if (this._chunks.length) {
                                const part = new Blob(this._chunks, { type: 'audio/webm' });
                                this._segments.push(part); // append after existing if any
                            }
                            this.isRecording = false;
                            this._rebuild();
                        };
                        this._recorder.start();
                        this.isRecording = true;
                        this.label = 'Recording...';
                    },

                    stop() {
                        if (this._recorder && this.isRecording) this._recorder.stop();
                    },

                    togglePlay() {
                        const a = this.$refs.audio;
                        if (!a?.src) return;
                        if (a.paused) {
                            a.play().then(() => { this.isPlaying = true; }).catch(() => {});
                        } else {
                            a.pause();
                            this.isPlaying = false;
                        }
                        a.onended = () => { this.isPlaying = false; };
                    },

                    clearClient() {
                        this._chunks = [];
                        this._segments = [];
                        this._loadedExisting = false;
                        this._rebuild();
                    },

                    async _uploadBlobToLivewire(blob) {
                        const file = new File([blob], `recording-${this.qid}.webm`, { type: 'audio/webm' });
                        const prop = `questions.${this.qid}.uploaded_sound`;

                        this.$wire.upload(
                            prop,
                            file,
                            () => { /* success, server will post-process in updated() */ },
                            () => { this.label = 'Upload failed, try again'; },
                            () => {}
                        );
                    },

                    _rebuild() {
                        const a = this.$refs.audio;

                        if (this._currentUrl) {
                            URL.revokeObjectURL(this._currentUrl);
                            this._currentUrl = null;
                        }

                        if (!this._segments.length) {
                            if (this.existingUrl && this._loadedExisting) {
                                a.src = this.existingUrl;
                                a.load();
                                this.label = 'Existing audio';
                                this.hasAudio = true;
                                return;
                            }
                            if (a) { a.removeAttribute('src'); a.load(); }
                            this.label = 'Can record, no sound file added';
                            this.hasAudio = false;
                            this.isPlaying = false;
                            return;
                        }

                        const blob = new Blob(this._segments, { type: 'audio/webm' });
                        const url  = URL.createObjectURL(blob);
                        this._currentUrl = url;

                        a.src = url;
                        a.load();
                        this.label = 'Sound file added, can still record to add to the sound';
                        this.hasAudio = true;

                        this._uploadBlobToLivewire(blob);
                    },
                };
            }
            </script>


    @endpush
</div>
