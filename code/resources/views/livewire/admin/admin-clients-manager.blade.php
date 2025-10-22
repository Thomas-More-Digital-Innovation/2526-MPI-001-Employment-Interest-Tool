<div
    x-data="{}"
    x-on:modal-open.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).show()"
    x-on:modal-close.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).close()"
    class="space-y-6">
    @if (session('status'))
    <div class="rounded-md bg-green-50 p-4 text-green-800">
        {{ session('status') }}
    </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-end-safe md:justify-between">
        <div class="flex-1">
            <flux:label class="block text-sm font-medium" for="client-search">
                {{ __('manage-clients.SearchClients') }}
            </flux:label>
            <flux:input
                id="client-search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ $showInactivated ? __('manage-clients.SearchClientBy') : __('manage-clients.SearchClientByAndActive') }}">
            </flux:input>
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="admin-client-form">

                <flux:button type="button" wire:click="startCreate" icon="user-plus" class="bg-color-mpi">
                    {{ __('manage-clients.AddClient') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="space-y-8">
        @forelse ($activeClientGroups as $group)
        <section class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <flux:heading size="md">
                    {{ $group['mentor_name'] }}
                </flux:heading>
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ trans_choice('{0}No clients|{1}:count client|[2,*]:count clients', $group['clients']->count(), ['count' => $group['clients']->count()]) }}
                </span>
            </div>

            @include('livewire.admin.admin-clients-manager-table', ['records' => $group['clients'], 'tableKey' => 'active-' . $group['mentor_id']])
        </section>
        @empty
        <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-gray-500">
            {{ __('manage-clients.NoClientsFound') }}
        </div>
        @endforelse
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:button
            type="button"
            icon="{{ $showInactivated ? 'eye-slash' : 'eye' }}"
            wire:click="toggleShowInactivated"
            class="bg-color-mpi">

            {{ $showInactivated ? __('manage-clients.HideInactive') :  __('manage-clients.ShowInactive') }}
        </flux:button>
    </div>
    @if ($showInactivated)
    <div class="mt-4">
        <div class="space-y-8">
            @forelse ($inactiveClientGroups as $group)
            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <flux:heading size="md">
                        {{ $group['mentor_name'] }}
                    </flux:heading>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                        {{ trans_choice('{0}No clients|{1}:count client|[2,*]:count clients', $group['clients']->count(), ['count' => $group['clients']->count()]) }}
                    </span>
                </div>

                @include('livewire.admin.admin-clients-manager-table', ['records' => $group['clients'], 'tableKey' => 'inactive-' . $group['mentor_id']])
            </section>
            @empty
            <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-gray-500">
                {{ __('manage-clients.NoInactiveFound') }}
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <flux:modal
        name="admin-client-form"
        class="max-w-3xl"
        x-on:close="$wire.call('closeFormModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $formModalMode === 'edit' ? __('manage-clients.EditClient') : __('manage-clients.AddClient') }}
            </flux:heading>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <flux:input
                            id="client-first-name"
                            type="text"
                            wire:model.defer="form.first_name"
                            :label="__('user.first_name')"
                            required
                            autofocus />
                        @error('form.first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            id="client-last-name"
                            type="text"
                            wire:model.defer="form.last_name"
                            required
                            :label="__('user.last_name')" />
                        @error('form.last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            id="client-username"
                            type="text"
                            wire:model.defer="form.username"
                            :label="__('user.username')"
                            required />
                        @error('form.username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            id="client-password"
                            type="password"
                            wire:model.defer="form.password"
                            :label="$editingId ? __('user.new_password_optional') : __('user.password')"
                            :required="!$editingId" />
                        @error('form.password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:checkbox
                            id="client-sound"
                            wire:model.defer="form.is_sound_on"
                            :label="$form['is_sound_on'] ? __('user.sound_on') : __('user.sound_off')" />
                        @error('form.is_sound_on')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:select
                            id="client-mentor"
                            wire:model.defer="form.mentor_id"
                            :label="__('Mentor')"
                            required>
                            <option value="">{{ __('manage-clients.noMentorAssigned') }}</option>
                            @foreach ($mentorOptions as $mentor)
                            <option value="{{ $mentor['id'] }}" @selected((string) $mentor['id']===(string) $form['mentor_id'])>
                                {{ $mentor['label'] }}
                            </option>
                            @endforeach
                        </flux:select>
                        @error('form.mentor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:select
                            id="client-vision-type"
                            wire:model.defer="form.vision_type"
                            :label="__('user.vision_type')"
                            required>
                            @forelse ($visionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @empty
                            <option value="">{{ __('No vision type assigned') }}</option>
                            @endforelse
                        </flux:select>
                        @error('form.vision_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:select
                            id="client-language"
                            wire:model.defer="form.language_id"
                            :label="__('Language')"
                            required>
                            @foreach ($languages as $language)
                            {{-- use the language of the users form to determine default language --}}
                            <option value="{{ $language['id'] }}" {{ $language['id'] === $form['language_id'] ? 'selected' : '' }}>
                                {{ $language['label'] }}
                            </option>
                            @endforeach
                        </flux:select>
                        @error('form.language_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="client-active" class="block text-sm font-medium">
                            {{ __('user.account_status') }}
                        </flux:label>
                        <div class="mt-2">
                            <flux:checkbox
                                id="client-active"
                                wire:model.defer="form.active"
                                :label="__('manage-clients.ClientCanSignIn')" />
                        </div>
                        @error('form.active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @if ($editingId && !$form["active"])
                    <flux:button
                        type="button"
                        variant="danger"
                        size="sm"
                        icon="trash"
                        wire:click="requestDelete({{ $editingId }})">
                        {{ __('manage-clients.deleteClient') }}
                    </flux:button>
                    @endif
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 md:flex-row md:items-center md:justify-end">
                    <flux:modal.close>
                        <flux:button
                            type="button"
                            variant="outline"
                            wire:click="cancelForm">
                            {{ __('manage-clients.Cancel') }}
                        </flux:button>
                    </flux:modal.close>

                    <flux:button
                        type="submit"
                        variant="primary">
                        {{ $editingId ? __('manage-clients.SaveChanges') : __('manage-clients.CreateClient') }}
                    </flux:button>
                </div>
            </form>
        </div>

    </flux:modal>

    <flux:modal
        name="admin-client-toggle"
        class="max-w-md"
        x-on:close="$wire.call('closeToggleModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $toggleModalWillActivate ? __('manage-clients.EnableClient') : __('manage-clients.DisableClient') }}
            </flux:heading>
            <flux:text class="text-sm text-gray-700">
                {{ $toggleModalWillActivate
                            ? __('manage-clients.EnableClientConfirmation', ['client' => $toggleModalName])
                            : __('manage-clients.DisableClientConfirmation', ['client' => $toggleModalName]) }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeToggleModal">
                        {{ __('manage-clients.Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="{{ $toggleModalWillActivate ? 'primary' : 'danger' }}"
                    wire:click="confirmToggle">
                    {{ $toggleModalWillActivate ? __('manage-clients.EnableClient') : __('manage-clients.DisableClient') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal
        name="admin-client-delete"
        class="max-w-md"
        x-on:close="$wire.call('closeDeleteModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ __('manage-clients.DeleteClient') }}
            </flux:heading>

            <flux:text class="text-sm text-gray-700">
                {{ __('manage-clients.ConfirmDeleteClient', ['client' => $toggleModalName]) }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeDeleteModal">
                        {{ __('manage-clients.Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="danger"
                    wire:click="confirmDelete">
                    {{ __('manage-clients.deleteClient') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

<livewire:staff.assign-tests-to-client-modal />
</div>
