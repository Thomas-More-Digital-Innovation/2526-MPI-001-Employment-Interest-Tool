<div
    x-data="{}"
    x-on:modal-open.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).show()"
    x-on:modal-close.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).close()"
    class="space-y-6">
    @if (session('status'))
        @php
            $statusType = session('status')['type'] ?? 'success';
            $statusMessage = session('status')['message'] ?? '';
        @endphp
        <div class="rounded-md p-4 text-sm {{ $statusType === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-50 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
            {{ $statusMessage }}
        </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-end-safe md:justify-between">
        <div class="flex-1">
            <flux:label class="block text-sm font-medium" for="search">
                {{ __('interestfield.search') }}
            </flux:label>
            <flux:input
                id="search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('interestfield.search') }}">
            </flux:input>
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="create-interest-field-form">
                <flux:button type="button" wire:click="startCreate" icon="user-plus" class="bg-color-mpi">
                    {{ __('interestfield.add') }}
                </flux:button>
            </flux:modal.trigger>
            <flux:button
                type="button"
                icon="{{ $showInactivated ? 'eye-slash' : 'eye' }}"
                wire:click="toggleShowInactivated"
                class="ml-2 bg-color-mpi">
                {{ $showInactivated ? __('interestfield.hide_inactive') :  __('interestfield.show_inactive') }}
            </flux:button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg shadow-sm">
        <x-table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-50 dark:bg-zinc-900">
                <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <th class="px-4 py-3">{{ __('interestfield.name') }}</th>
                    <th class="px-4 py-3">{{ __('interestfield.description') }}</th>
                    <th class="px-4 py-3">{{ __('interestfield.active_label') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('interestfield.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-900 dark:text-gray-50">
                @forelse ($records as $interestField)
                <tr wire:key="interest-field-{{ $interestField->interest_field_id }}" class="hover:bg-gray-50 hover:dark:bg-zinc-600">
                    <td class="px-4 py-3">{{ $interestField->getName(app()->getLocale()) }}</td>
                    <td class="px-4 py-3">{{ $interestField->getDescription(app()->getLocale()) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $interestField->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $interestField->active ? __('interestfield.active_label') : __('interestfield.inactive_label') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <flux:modal.trigger name="create-interest-field-form">
                                <flux:button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    icon="pencil"
                                    wire:click="startEdit({{ $interestField->interest_field_id }})">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>

                            @if ($interestField->questions()->exists())
                                <flux:tooltip content="{{ __('interestfield.cannot_delete_used') }}">
                                    <flux:button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        icon="trash"
                                        class="cursor-not-allowed text-gray-400! text:color-gray-600"
                                        >
                                        {{ __('Delete') }}
                                    </flux:button>
                                </flux:tooltip>
                            @else
                                <flux:button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    icon="trash"
                                    wire:click="confirmDelete({{ $interestField->interest_field_id }})">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                        {{ __('interestfield.no_fields_found') }}
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

    @if ($showInactivated)
    <div class="mt-6">
        <h2 class="text-lg font-medium mb-3">{{ __('interestfield.inactive_heading') }}</h2>
        <div class="overflow-x-auto rounded-lg shadow-sm">
            <x-table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-50 dark:bg-zinc-900">
                    <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <th class="px-4 py-3">{{ __('interestfield.name') }}</th>
                        <th class="px-4 py-3">{{ __('interestfield.description') }}</th>
                        <th class="px-4 py-3">{{ __('interestfield.active_label') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('interestfield.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-900 dark:text-gray-50">
                    @forelse ($this->inactiveRecords as $interestField)
                    <tr wire:key="interest-field-inactive-{{ $interestField->interest_field_id }}" class="hover:bg-gray-50 hover:dark:bg-zinc-600">
                        <td class="px-4 py-3">{{ $interestField->getName(app()->getLocale()) }}</td>
                        <td class="px-4 py-3">{{ $interestField->getDescription(app()->getLocale()) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $interestField->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $interestField->active ? __('interestfield.active_label') : __('interestfield.inactive_label') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <flux:modal.trigger name="create-interest-field-form">
                                    <flux:button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        icon="pencil"
                                        wire:click="startEdit({{ $interestField->interest_field_id }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                </flux:modal.trigger>

                                @if ($interestField->questions()->exists())
                                    <flux:tooltip content="{{ __('interestfield.cannot_delete_used') }}">
                                        <flux:button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            icon="trash"
                                            class="cursor-not-allowed text-gray-400! text:color-gray-600"
                                            >
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </flux:tooltip>
                                @else
                                    <flux:button
                                        type="button"
                                        variant="danger"
                                        size="sm"
                                        icon="trash"
                                        wire:click="confirmDelete({{ $interestField->interest_field_id }})">
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('interestfield.no_fields_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </div>
    @endif

    @if ($showInactivated)
    <div class="mt-4">
        {{ $this->inactiveRecords->links() }}
    </div>
    @endif

    <!-- Add Interest Field Modal -->
    <flux:modal
        name="create-interest-field-form"
        class="max-w-2xl"
        x-on:close="$wire.call('cancelForm')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('interestfield.edit') : __('interestfield.add') }}
            </flux:heading>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <flux:input
                            id="interest-field-name-default"
                            type="text"
                            wire:model.defer="form.name"
                            :label="__('interestfield.name') . ' (' . __('interestfield.default') . ')'"
                            required
                        />
                        @error('form.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:textarea
                            id="interest-field-description-default"
                            wire:model.defer="form.description"
                            :label="__('interestfield.description') . ' (' . __('interestfield.default') . ')'"
                            rows="3"
                            required
                        />
                        @error('form.description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:checkbox
                            id="interest-field-active"
                            wire:model.defer="form.active"
                            :label="__('interestfield.active_label')"
                        />
                        @error('form.active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($editingId)
                        <div>
                            <h1>{{ __('interestfield.add_translation') }}</h1>
                            <div class="flex justify-end items-center gap-2">
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

                                <flux:button
                                    type="button"
                                    class="bg-blue-600 hover:bg-blue-700 text-white"
                                    wire:click="addTranslation">
                                    {{ __('Add') }}
                                </flux:button>
                            </div>
                        </div>

                        @foreach ($form['translations'] as $languageCode => $translation)
                            <div class="border rounded-md p-4 mb-4" x-data="{ open: false }">
                                <div class="flex justify-between items-center">
                                    <div x-show="!open" class="flex items-center gap-2">
                                        <flux:button
                                            type="button"
                                            icon="chevron-down"
                                            x-on:click="open = true">
                                        </flux:button>
                                        <h2 class="text-lg font-medium">{{ $availableLanguages[$languageCode] ?? $languageCode }}</h2>
                                    </div>
                                    <div x-show="open" class="flex items-center gap-2">
                                        <flux:button
                                            type="button"
                                            icon="chevron-up"
                                            x-on:click="open = false">
                                        </flux:button>
                                        <h2 class="text-lg font-medium">{{ $availableLanguages[$languageCode] ?? $languageCode }}</h2>
                                    </div>
                                    <flux:button
                                        type="button"
                                        icon="x-mark"
                                        variant="danger"
                                        size="sm"
                                        class="ml-auto"
                                        wire:click="removeTranslation('{{ $languageCode }}')"/>
                                </div>

                                <div x-show="open" class="mt-4 space-y-4">
                                    <div>
                                        <flux:input
                                            wire:model.defer="form.translations.{{ $languageCode }}.name"
                                            :label="__('interestfield.name')"
                                            required
                                        />
                                        @error('form.translations.' . $languageCode . '.name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <flux:textarea
                                            wire:model.defer="form.translations.{{ $languageCode }}.description"
                                            :label="__('interestfield.description')"
                                            rows="3"
                                            required
                                        />
                                        @error('form.translations.' . $languageCode . '.description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">
                            {{ __('Cancel') }}
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" class="bg-color-mpi">
                        {{ $editingId ? __('interestfield.updatebtn') : __('interestfield.addbtn') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Interest Field Confirmation Modal -->
    <flux:modal
        name="delete-interest-field-confirmation"
        class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ __('interestfield.delete_heading') }}
            </flux:heading>

            <p>{{ __('interestfield.delete_confirm') }}</p>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="danger"
                    wire:click="deleteInterestField">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

