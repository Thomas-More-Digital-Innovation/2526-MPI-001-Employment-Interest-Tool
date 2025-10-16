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
            <flux:label class="block text-sm font-medium" for="org-search">
                {{ __('organisations.search') }}
            </flux:label>
            <flux:input
                id="org-search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('organisations.search_placeholder') }}" />
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="organisation-form">
                <flux:button type="button" wire:click="startCreate" icon="building" class="bg-color-mpi">
                    {{ __('organisations.add') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    @include('livewire.superadmin.organisations-manager-table', ['records' => $records])

    <flux:modal
        name="organisation-form"
        class="max-w-3xl"
        x-on:close="$wire.call('resetFormState')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('organisations.edit') : __('organisations.add') }}
            </flux:heading>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <flux:input
                            id="org-name"
                            type="text"
                            wire:model.defer="form.name"
                            :label="__('organisations.name')"
                            required
                            autofocus />
                        @error('form.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            id="org-expire"
                            type="date"
                            wire:model.defer="form.expire_date"
                            :label="__('organisations.expire_date')" />
                        @error('form.expire_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:label for="org-active" class="block text-sm font-medium">
                            {{ __('organisations.status') }}
                        </flux:label>
                        <div class="mt-2">
                            <flux:checkbox
                                id="org-active"
                                wire:model.defer="form.active"
                                :label="__('organisations.active')" />
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
                            {{ __('organisations.cancel') }}
                        </flux:button>
                    </flux:modal.close>

                    <flux:button
                        type="submit"
                        variant="primary">
                        {{ $editingId ? __('organisations.save_changes') : __('organisations.create_organisation') }}
                    </flux:button>
                </div>
            </form>
        </div>

    </flux:modal>

    <flux:modal
        name="organisation-deactivate-confirm"
        class="max-w-md"
        x-on:close="$wire.call('resetFormState')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ __('organisations.confirm') }}
            </flux:heading>

            <flux:text class="text-sm text-gray-700">
                {{ __('organisations.deactivate_confirm') }}
            </flux:text>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                <flux:modal.close>
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="resetFormState">
                        {{ __('organisations.cancel') }}
                    </flux:button>
                </flux:modal.close>

                    <flux:button
                        type="button"
                        variant="danger"
                        wire:click="confirmToggle">
                        {{ __('organisations.confirm') }}
                    </flux:button>
            </div>
        </div>
    </flux:modal>

    @include('livewire.superadmin.manage-organisation-admins')

</div>
