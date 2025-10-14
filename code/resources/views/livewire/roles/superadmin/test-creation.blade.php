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
                        <div
                            wire:key="audio-box-{{ $selectedQuestion }}"
                            x-data="audioRec({ qid: {{ $selectedQuestion }} })"
                            x-init="init()"
                            class="flex flex-col gap-3 p-3 m-2 rounded-2xl bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600"
                        >
                            <div class="flex items-center gap-3 flex-wrap">
                                <!-- Record Button -->
                                <button
                                    type="button"
                                    class="px-4 py-2 rounded-xl text-white bg-red-600 active:opacity-80"
                                    x-text="isRecording ? 'Recording..' : 'Record'"
                                    @pointerdown.prevent="start()"
                                    @pointerup="stop()"
                                    @pointerleave="stop()"
                                ></button>

                                <!-- Play Button -->
                                <button
                                    type="button"
                                    class="px-4 py-2 rounded-xl border"
                                    x-show="hasAudio"
                                    x-text="isPlaying ? 'Pause' : 'Play'"
                                    @click="togglePlay()"
                                ></button>

                                <!-- File Upload -->
                                <input
                                    type="file"
                                    accept="audio/*"
                                    class="hidden"
                                    :id="`soundInput-${qid}`"
                                    :name="`soundInput-${qid}`"
                                    :key="`soundInput-${qid}`"
                                    :wire:model="`questions.${qid}.uploaded_sound`"
                                />
                                <label :for="`soundInput-${qid}`">
                                    <span class="px-4 py-2 rounded-xl border cursor-pointer">Browse</span>
                                </label>

                                <!-- Clear Button -->
                                <flux:button
                                    variant="ghost"
                                    class="p-2"
                                    title="Remove audio"
                                    @click.prevent="clearClient(); $wire.set('questions.'+qid+'.sound_link', null)"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M10 11v6M14 11v6M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </flux:button>
                            </div>

                            <!-- Audio Element -->
                            <audio x-ref="audio" class="hidden" preload="metadata"></audio>

                            <!-- Validation Error -->
                            @error('questions.'.$selectedQuestion.'.uploaded_sound')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror

                            <!-- Debug Info -->
                            <div class="text-xs text-zinc-400">
                                <p>sound_link: {{ data_get($questions[$selectedQuestion] ?? [], 'sound_link', 'null') }}</p>
                                <p>hasAudio: <span x-text="hasAudio"></span></p>
                                <p>isRecording: <span x-text="isRecording"></span></p>
                                <p>isPlaying: <span x-text="isPlaying"></span></p>
                            </div>
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
            window.audioRec = (cfg) => ({
                qid: cfg.qid,

                isRecording: false,
                isPlaying: false,
                label: 'Can record, no sound file added',
                hasAudio: false,

                _stream: null,
                _recorder: null,
                _chunks: [],
                _segments: [],

                init() {
                document.addEventListener('livewire:message.processed', () => this._rebuild());
                this._rebuild();
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
                this._recorder = new MediaRecorder(this._stream);
                this._recorder.ondataavailable = e => this._chunks.push(e.data);
                this._recorder.onstop = () => {
                    if (this._chunks.length) this._segments.push(new Blob(this._chunks, { type: 'audio/webm' }));
                    this.isRecording = false;
                    this._rebuild();
                };
                this._recorder.start();
                this.isRecording = true;
                },

                stop() {
                if (this._recorder && this.isRecording) this._recorder.stop();
                },

                togglePlay() {
                const a = this.$refs.audio;
                if (!a?.src) return;
                if (a.paused) {
                    a.play().catch(() => {});
                    this.isPlaying = true;
                } else {
                    a.pause();
                    this.isPlaying = false;
                }
                a.onended = () => { this.isPlaying = false; };
                },

                clearClient() {
                this._chunks = [];
                this._segments = [];
                this._rebuild();
                },

                _uploadBlobToLivewire(blob) {
                const file = new File([blob], `recording-${this.qid}.webm`, { type: 'audio/webm' });
                // build the property path with qid at runtime — no Blade inside template strings
                const prop = 'questions.' + this.qid + '.uploaded_sound';
                $wire.upload(prop, file, () => {}, () => {}, () => {});
                },

                _rebuild() {
                const a = this.$refs.audio;
                if (!this._segments.length) {
                    if (a) { a.removeAttribute('src'); a.load(); }
                    this.label = 'Can record, no sound file added';
                    this.hasAudio = false;
                    return;
                }
                const blob = new Blob(this._segments, { type: 'audio/webm' });
                const url  = URL.createObjectURL(blob);
                a.src = url;
                this.label = 'Sound file added, can still record to add to the sound';
                this.hasAudio = true;

                this._uploadBlobToLivewire(blob); // triggers parent updated -> uploadSound(index)
                },
            });
            </script>

    @endpush
</div>
