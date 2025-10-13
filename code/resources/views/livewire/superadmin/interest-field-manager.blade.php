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
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white shadow-sm">
        <x-table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-50 dark:bg-zinc-600">
                <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <th class="px-4 py-3">{{ __('interestfield.name') }}</th>
                    <th class="px-4 py-3">{{ __('interestfield.description') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('interestfield.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-500 dark:text-gray-50">
                @forelse ($records as $interestField)
                <tr wire:key="interest-field-{{ $interestField->interest_field_id }}" class="hover:bg-gray-50 hover:dark:bg-zinc-600">
                    <td class="px-4 py-3">{{ $interestField->name }}</td>
                    <td class="px-4 py-3">{{ $interestField->description }}</td>
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
                            <flux:button
                                type="button"
                                variant="outline"
                                size="sm"
                                icon="trash"
                                wire:click="confirmDelete({{ $interestField->interest_field_id }})">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                        {{ __('No interest fields found yet.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>

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
                            id="interest-field-name"
                            type="text"
                            wire:model.defer="form.name"
                            :label="__('interestfield.name')"
                            required
                            autofocus />
                        @error('form.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:textarea
                            id="interest-field-description"
                            wire:model.defer="form.description"
                            :label="__('interestfield.description')"
                            rows="3"
                            required />
                        @error('form.description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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

