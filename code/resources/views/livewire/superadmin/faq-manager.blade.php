<div
    x-data="{}"
    x-on:modal-open.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).show()"
    x-on:modal-close.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).close()"
    class="space-y-6">
    @if (session('status'))
        <div class="rounded-md p-4 text-sm bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-end-safe md:justify-between">
        <div class="flex-1">
            <flux:label class="block text-sm font-medium" for="search">
                {{ __('faq.search') }}
            </flux:label>
            <flux:input
                id="search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('faq.search') }}">
            </flux:input>
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="create-faq-form">
                <flux:button type="button" wire:click="startCreate" icon="plus" class="bg-color-mpi">
                    {{ __('faq.add') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg shadow-sm">
        <x-table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
                <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <th class="px-4 py-3">{{ __('faq.question') }}</th>
                    <th class="px-4 py-3">{{ __('faq.answer') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('faq.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-900 dark:text-gray-50">
                @forelse ($records as $faq)
                <tr wire:key="faq-{{ $faq->frequently_asked_question_id  }}" class="hover:bg-gray-50 hover:dark:bg-zinc-600">
                    <td class="px-4 py-3">{{ $faq->question }}</td>
                    <td class="px-4 py-3">{{ $faq->answer }}</td>
                    <td class="px-4 py-3 text-right">
                        <flux:button type="button" icon="pencil" wire:click="startEdit({{ $faq->frequently_asked_question_id }})" class="mr-2">
                            {{ __('Edit') }}
                        </flux:button>
                        <flux:button type="button" icon="trash" variant="danger" wire:click="confirmDelete({{ $faq->frequently_asked_question_id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                        {{ __('faq.empty') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $records->links() }}
    </div>

    <!-- Add/Edit FAQ Modal -->
    <flux:modal
        name="create-faq-form"
        class="max-w-3xl"
        x-on:close="$wire.call('resetFormState')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('faq.edit') : __('faq.add') }}
            </flux:heading>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <flux:label for="question">{{ __('faq.question') }}</flux:label>
                        <flux:input id="question" type="text" wire:model.defer="form.question" required />
                        @error('form.question')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <flux:label for="answer">{{ __('faq.answer') }}</flux:label>
                        <flux:textarea id="answer" wire:model.defer="form.answer" required />
                        @error('form.answer')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-lg font-semibold mb-2">{{ __('faq.translations') }}</h3>

                    <div class="flex items-center gap-2 mb-4">
                        <flux:select
                            id="new-translation-language"
                            wire:model="newTranslationLanguage">
                            <option value="" disabled selected>{{ __('interestfield.select_language') }}</option>
                            @foreach ($availableLanguages as $languageCode => $languageName)
                                 @if (!isset($form['translations'][$languageCode]))
                                    <option value="{{ $languageCode }}">
                                        {{ $languageName }}
                                    </option>
                                @endif
                            @endforeach
                        </flux:select>
                        <flux:button type="button" wire:click="addTranslation" class="bg-color-mpi">
                            {{ __('Add translation') }}
                        </flux:button>
                    </div>

                    @foreach ($form['translations'] as $langId => $translation)
                        <div class="border rounded p-4 mb-2">
                            <div class="font-bold mb-2">{{ $availableLanguages[$langId] ?? $langId }}</div>
                            <div class="mb-2">
                                <flux:label for="translation-question-{{ $langId }}">{{ __('faq.question') }} ({{ $availableLanguages[$langId] ?? $langId }})</flux:label>
                                <flux:input id="translation-question-{{ $langId }}" type="text" wire:model.defer="form.translations.{{ $langId }}.question" />
                            </div>
                            <div>
                                <flux:label for="translation-answer-{{ $langId }}">{{ __('faq.answer') }} ({{ $availableLanguages[$langId] ?? $langId }})</flux:label>
                                <flux:textarea id="translation-answer-{{ $langId }}" wire:model.defer="form.translations.{{ $langId }}.answer" />
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">
                            {{ __('Cancel') }}
                        </flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" class="bg-color-mpi">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete FAQ Confirmation Modal -->
    <flux:modal
        name="delete-faq-confirmation"
        class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ __('faq.delete_heading') }}
            </flux:heading>
            <p>{{ __('faq.delete_confirm') }}</p>
            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button
                    type="button"
                    variant="danger"
                    wire:click="deleteFaq">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
