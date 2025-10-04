<!-- Restore Confirmation Modal -->
<flux:modal name="restore-client-{{ $clientId }}" :show="$showModal" focusable class="max-w-md">
    <div class="p-6">
        <flux:heading size="lg">{{ __('Confirm Reactivation') }}</flux:heading>
        <flux:subheading>{{ __('Are you sure you want to reactivate this client?') }}</flux:subheading>

        <p class="mt-4 text-sm text-gray-600">
            {{ __('This action will make the client active again. You can deactivate them later if needed.') }}
        </p>
    </div>

    <div class="flex justify-end space-x-2 rtl:space-x-reverse px-6 py-4 bg-gray-50 border-t">
        <flux:modal.close>
            <flux:button variant="ghost" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
        </flux:modal.close>
        <flux:button wire:click="restoreClient" variant="primary">{{ __('Reactivate Client') }}</flux:button>
    </div>
</flux:modal>
