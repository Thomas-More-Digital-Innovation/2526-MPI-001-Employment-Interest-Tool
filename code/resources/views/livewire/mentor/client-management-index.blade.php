<div>
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search clients...') }}"
                    icon="magnifying-glass"
                />
            </div>
            <flux:button wire:click="resetSearch" variant="ghost" icon="x-mark">
                {{ __('Clear') }}
            </flux:button>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Clients') }}</h3>
    </div>

    <div class="overflow-x-auto">
        @forelse($clients as $client)
            <div class="border-b border-gray-200 last:border-b-0">
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
                        <!-- Client Info -->
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $client->initials() }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $client->first_name }} {{ $client->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $client->vision_type }}
                                </div>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="text-sm text-gray-900">{{ $client->username }}</div>

                        <!-- Email -->
                        <div class="text-sm text-gray-500">{{ $client->email ?: __('No email') }}</div>

                        <!-- Organisation -->
                        <div class="text-sm text-gray-500">{{ $client->organisation->organisation_name ?? __('No organisation') }}</div>

                        <!-- Language -->
                        <div class="text-sm text-gray-500">{{ $client->language->language_name ?? __('No language') }}</div>

                        <!-- Status & Actions -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($client->active)
                                    <flux:badge color="green">{{ __('Active') }}</flux:badge>
                                @else
                                    <flux:badge color="red">{{ __('Inactive') }}</flux:badge>
                                @endif
                            </div>

                            <div class="flex space-x-2">
                                @if($client->active)
                                    <flux:modal.trigger name="edit-client-{{ $client->user_id }}">
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="pencil"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'edit-client-{{ $client->user_id }}')"
                                        >
                                            {{ __('Edit') }}
                                        </flux:button>
                                    </flux:modal.trigger>
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        wire:click="confirmDelete('{{ $client->user_id }}')"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                @else
                                    <flux:modal.trigger name="restore-client-{{ $client->user_id }}">
                                        <flux:button
                                            size="sm"
                                            variant="primary"
                                            icon="arrow-uturn-down"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'restore-client-{{ $client->user_id }}')"
                                        >
                                            {{ __('Restore') }}
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <div class="text-gray-500">{{ __('No clients found.') }}</div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $clients->links() }}
        </div>
    @endif
    </div>
</div>
