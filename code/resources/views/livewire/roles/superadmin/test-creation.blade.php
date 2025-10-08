<div class="flex gap-4">
    <main class="w-3/4">
        <form wire:submit.prevent class="space-y-4" wire:key="editor-{{ $questions[$selectedQuestion]['uid'] ?? 'none' }}">
            <flux:input wire:model.defer="test_name" placeholder="Test Name" label="Test Name" type="text" />

            <div class="flex bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-2xl min-h-[40vh]">
                <div class="bg-zinc-300 dark:bg-zinc-600 rounded-2xl flex-1 flex items-center justify-center m-4">
                    <flux:button type="button" variant="primary" class="!rounded-full text-xl px-4 py-3" color="zinc">+</flux:button>
                </div>

                <div class="flex-1 flex-col m-4 p-2">
                    <div class="mb-5">
                        <flux:input
                            class="mb-2"
                            wire:model.defer="questions.{{ $selectedQuestion }}.title"
                            placeholder="Title"
                            label="Title"
                            type="text"
                        />
                        <flux:textarea
                            wire:model.defer="questions.{{ $selectedQuestion }}.description"
                            placeholder="Description"
                            label="Description"
                        />
                    </div>

                    <div>
                        <flux:select
                            wire:model="questions.{{ $selectedQuestion }}.interest"
                            placeholder="Choose interest field..."
                        >
                            @foreach ($interestFields as $interestField)
                                <flux:select.option value="{{ $interestField->id }}">{{ $interestField->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>
        </form>
    </main>
    <aside class="w-1/4 p-3">
        <div class="mt-3 bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 pl-2 rounded-xl flex items-center justify-between mb-4">
            <flux:heading size="lg">Questions</flux:heading>
            <flux:button
                type="button"
                wire:click.stop="createQuestion"
                wire:loading.attr="disabled"
                variant="primary"
                color="green"
                class=""
            >+</flux:button>
        </div>

        <ol class="space-y-2">
            @foreach ($questions as $i => $q)
                <li wire:key="q-{{ $q['uid'] }}" class="flex items-center gap-2">
                    <flux:button
                        type="button"
                        variant="{{ $selectedQuestion === $i ? 'outline' : 'subtle' }}"
                        class="flex-1 justify-start"
                        wire:click="selectQuestion({{ $i }})"
                    >
                        {{ 'Q' . ($i + 1) }} {{ $q['title'] ? '• ' . \Illuminate\Support\Str::limit($q['title'], 24) : '• Untitled' }}
                    </flux:button>
                    <flux:button type="button" size="sm" variant="ghost" wire:click="moveQuestionUp({{ $i }})">↑</flux:button>
                    <flux:button type="button" size="sm" variant="ghost" wire:click="moveQuestionDown({{ $i }})">↓</flux:button>
                    <flux:button type="button" size="sm" variant="danger" wire:click="removeQuestion({{ $i }})">✕</flux:button>
                </li>
            @endforeach
        </ol>
    </aside>
</div>
