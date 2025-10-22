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
            <flux:label class="block text-sm font-medium" for="mentor-search">
                {{__('manage-researchers.searchResearcher')}}
            </flux:label>
            <flux:input
                id="mentor-search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ $showInactivated ? __('manage-researchers.SearchResearcherBy') : __('manage-researchers.SearchResearcherByAndActive') }}">
            </flux:input>
        </div>
        <div class="flex-shrink-0 content-end">
            <flux:modal.trigger name="admin-client-form">
                <flux:button type="button" wire:click="startCreate" icon="user-plus" class="bg-color-mpi">
                    {{ __('manage-researchers.addResearcher') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="space-y-8">
        @forelse ($activeClientGroups as $group)
            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ trans_choice('{0}No researcher|{1}:count researcher|[2,*]:count researchers', $group['clients']->count(), ['count' => $group['clients']->count()]) }}

                </span>
                </div>
                @include('livewire.admin.mentor-manager-table', ['records' => $group['clients'], 'tableKey' => 'active-' . $group['mentor_id']])
            </section>
        @empty
            <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-gray-500">
                {{ __('manage-researchers.noResearchersFound') }}
            </div>
        @endforelse
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:button
            type="button"
            icon="{{ $showInactivated ? 'eye-slash' : 'eye' }}"
            wire:click="toggleShowInactivated"
            class="bg-color-mpi">

            {{ $showInactivated ? __('manage-researchers.hideInactive') :  __('manage-researchers.showInactive') }}
        </flux:button>
    </div>
    @if ($showInactivated)
        <div class="mt-4">
            <div class="space-y-8">
                @forelse ($inactiveClientGroups as $group)
                    <section class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                        {{ trans_choice('{0}No mentors|{1}:count mentor|[2,*]:count mentors', $group['clients']->count(), ['count' => $group['clients']->count()]) }}
                    </span>
                        </div>

                        @include('livewire.admin.admin-clients-manager-table', ['records' => $group['clients'], 'tableKey' => 'inactive-' . $group['mentor_id']])
                    </section>
                @empty
                    <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-gray-500">
                        {{ __('manage-researchers.noInactiveFound') }}
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Researcher Form Modal Component --}}
    <livewire:super-admin.researcher-form-modal />

    {{-- Toggle Confirmation Modal --}}
    <flux:modal
        name="admin-client-toggle"
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

            <div class="flex justify-end gap-3 pt-4">
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

    {{-- Delete Confirmation Modal --}}
    <flux:modal
        name="admin-client-delete"
        class="max-w-md"
        x-on:close="$wire.call('closeDeleteModal')">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ __('manage-researchers.deleteResearcher') }}
            </flux:heading>

            <flux:text class="text-sm text-gray-700">
                {{ __('Are you sure you want to delete :researcher? This action is irreversible.', ['researcher' => $deleteModalName]) }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button
                        type="button"
                        variant="outline"
                        wire:click="closeDeleteModal">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="danger"
                    wire:click="confirmDelete">
                    {{ __('manage-researchers.deleteResearcher') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
