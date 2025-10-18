<div class="px-4 py-5 sm:px-6">

    <div class="flex justify-between gap-4">
        <flux:heading class="py-1" size="xl">{{ __('admins.manager') }} - {{ $organisation->name }}</flux:heading>
        <flux:button href="{{ route('superadmin.organisations-manager') }}" class="bg-color-mpi" icon="arrow-left" variant="outline">{{ __('admins.back') }}</flux:button>
    </div>
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
                <flux:label class="block text-sm font-medium" for="admin-search">
                    {{ __('Search') }}
                </flux:label>
                <flux:input
                    id="admin-search"
                    type="search"
                    icon="magnifying-glass"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admins.search_placeholder') }}" />
            </div>
            <div class="flex-shrink-0 content-end">
                <flux:modal.trigger name="admin-form">
                    <flux:button type="button" wire:click="startCreate" icon="user-plus" class="bg-color-mpi">
                        {{ __('Add') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        @include('livewire.superadmin.admins-manager-table', ['records' => $records])

        <flux:modal
            name="admin-form"
            class="max-w-3xl"
            x-on:close="$wire.call('resetFormState')">
            <div class="space-y-6">
                <flux:heading size="lg">
                    {{ $editingId ? __('Edit') : __('Add') }}
                </flux:heading>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <flux:input
                                id="admin-first"
                                type="text"
                                wire:model.defer="form.first_name"
                                :label="__('First Name')"
                                required
                                autofocus />
                            @error('form.first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                id="admin-last"
                                type="text"
                                wire:model.defer="form.last_name"
                                :label="__('Last Name')" />
                            @error('form.last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                id="admin-username"
                                type="text"
                                wire:model.defer="form.username"
                                :label="__('admins.username')"
                                required />
                            @error('form.username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                id="admin-email"
                                type="email"
                                wire:model.defer="form.email"
                                :label="__('Email')"
                                required />
                            @error('form.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                id="admin-password"
                                type="password"
                                wire:model.defer="form.password"
                                :label="__('Password')"
                                :placeholder="$editingId ? __('admins.leave_blank_to_keep') : ''" />
                            @error('form.password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <flux:label for="admin-active" class="block text-sm font-medium">
                                {{ __('Status') }}
                            </flux:label>
                            <div class="mt-2">
                                <flux:checkbox
                                    id="admin-active"
                                    wire:model.defer="form.active"
                                    :label="__('Active')" />
                            </div>
                            @error('form.active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Organisation is managed via OrganisationsManager; admins are tied to the organisation in session. --}}

                        <div class="md:col-span-2">
                            <flux:select wire:model.defer="form.language_id" :label="__('Language')" required>
                                @foreach($this->languages as $lang)
                                    <option value="{{ $lang->language_id }}">{{ $lang->language_name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 md:flex-row md:items-center md:justify-end">
                        <flux:modal.close>
                            <flux:button
                                type="button"
                                variant="outline"
                                wire:click="cancelForm">
                                {{ __('Cancel') }}
                            </flux:button>
                        </flux:modal.close>

                        <flux:button
                            type="submit"
                            variant="primary">
                            {{ $editingId ? __('Save Changes') : __('Create') }}
                        </flux:button>
                    </div>
                </form>
            </div>

        </flux:modal>

        <flux:modal
            name="admin-delete-confirm"
            class="max-w-md"
            x-on:close="$wire.call('cancelRemove')">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('admins.confirm_delete') ?? 'Confirm' }}</flux:heading>

                <flux:text class="text-sm">{{ __('admins.delete_confirm_text') ?? 'Are you sure you want to remove admin role from this user?' }}</flux:text>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <flux:modal.close>
                        <flux:button type="button" variant="outline" wire:click="cancelRemove">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="button" variant="danger" wire:click="confirmRemoveAdmin">{{ __('Confirm') }}</flux:button>
                </div>
            </div>
        </flux:modal>

    </div>
</div>
