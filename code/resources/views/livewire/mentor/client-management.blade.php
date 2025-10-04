<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Client Management') }}</h1>
            <p class="text-gray-600">{{ __('Manage your assigned clients') }}</p>
        </div>
        <flux:modal.trigger name="create-client">
            <flux:button variant="primary" icon="plus" x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-client')">
                {{ __('Add Client') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Client Management Components -->
    <div>
        @livewire('mentor.client-management-index', ['refreshKey' => $refreshKey], key('client-management-index-' . $refreshKey))

        @livewire('mentor.client-create-modal', key($refreshKey))
        @livewire('mentor.client-edit-modal', key($refreshKey))
        @livewire('mentor.client-delete-modal', key($refreshKey))
        @livewire('mentor.client-restore-modal', key($refreshKey))
    </div>
</div>
