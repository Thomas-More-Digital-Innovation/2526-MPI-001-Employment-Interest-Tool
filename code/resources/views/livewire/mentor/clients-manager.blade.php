<div
    x-data="{}"
    x-on:modal-open.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).show()"
    x-on:modal-close.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).hide()"
    class="space-y-6">
    @if (session('status'))
    <div class="rounded-md bg-green-50 p-4 text-green-800">
        {{ session('status') }}
    </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-end-safe md:justify-between">
        <div class="flex-1">
            <flux:label class="block text-sm font-medium" for="client-search">
                {{ __('Search clients') }}
            </flux:label>
            <flux:input
                id="client-search"
                type="search"
                icon="magnifying-glass"
                wire:model.debounce.400ms="search"
                placeholder="{{ $showInactivated ? __('Search all clients by name or username') : __('Search active clients by name or username') }}" />
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="mentor-client-form">

                <flux:button type="button" wire:click="startCreate" icon="user-plus" class="bg-color-mpi">
                    {{ __('Add client') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    @include('livewire.mentor.clients-manager-table', ['records' => $records])

    <div class="flex flex-wrap items-center gap-3">
        <flux:button
            type="button"
            icon="{{ $showInactivated ? 'eye-slash' : 'eye' }}"
            wire:click="toggleShowInactivated"
            class="bg-color-mpi-500 text-amber-50">

            {{ $showInactivated ? __('Hide inactivated clients') :  __('Show inactivated clients') }}
        </flux:button>
    </div>
    @if ($showInactivated)
    <div class="mt-4">
        @include('livewire.mentor.clients-manager-table', ['records' => $inactivatedClients])
    </div>
    @endif

    <flux:modal
        name="mentor-client-form"
        class="max-w-3xl"
        x-on:close="$wire.call('closeFormModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $formModalMode === 'edit' ? __('Edit client') : __('Add client') }}
            </flux:heading>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <flux:input
                            id="client-first-name"
                            type="text"
                            wire:model.defer="form.first_name"
                            :label="__('First name')"
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
                            :label="__('Last name (optional)')" />
                        @error('form.last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            id="client-username"
                            type="text"
                            wire:model.defer="form.username"
                            :label="__('Username')"
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
                            :label="$editingId ? __('New password (leave blank to keep current)') : __('Password')"
                            :required="!$editingId"
                            />
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
                            id="client-vision-type"
                            wire:model.defer="form.vision_type"
                            :label="__('user.vision_type')"
                            required>
                            @foreach ($visionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
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
                            {{ __('Active') }}
                        </flux:label>
                        <div class="mt-2">
                            <flux:checkbox
                                id="client-active"
                                wire:model.defer="form.active"
                                :label="__('Client can sign in')" />
                        </div>
                        @error('form.active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        {{ $editingId ? __('Save changes') : __('Create client') }}
                    </flux:button>
                </div>
            </form>
        </div>

    </flux:modal>

    <flux:modal
        name="mentor-client-toggle"
        class="max-w-md"
        x-on:close="$wire.call('closeToggleModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $toggleModalWillActivate ? __('Enable client') : __('Disable client') }}
            </flux:heading>

            <flux:text class="text-sm text-gray-700">
                {{ $toggleModalWillActivate
                            ? __('Are you sure you want to enable :client? They will regain access immediately.', ['client' => $toggleModalName])
                            : __('Are you sure you want to disable :client? They will lose access until re-enabled.', ['client' => $toggleModalName]) }}
            </flux:text>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                <flux:modal.close>
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeToggleModal">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="{{ $toggleModalWillActivate ? 'primary' : 'danger' }}"
                    wire:click="confirmToggle">
                    {{ $toggleModalWillActivate ? __('Enable client') : __('Disable client') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>