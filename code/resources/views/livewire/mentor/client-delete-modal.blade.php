<!-- Delete Confirmation Modal -->
<flux:modal name="delete-client-{{ $clientId }}" :show="$showModal" focusable class="max-w-md">
    <div class="p-6">
        <flux:heading size="lg">{{ __('Confirm Deactivation') }}</flux:heading>
        <flux:subheading>{{ __('Are you sure you want to deactivate this client?') }}</flux:subheading>

        <p class="mt-4 text-sm text-gray-600">
            {{ __('This action will make the client inactive but preserve their data. You can reactivate them later if needed.') }}
        </p>
    </div>

    <div class="flex justify-end space-x-2 rtl:space-x-reverse px-6 py-4 bg-gray-50 border-t">
        <flux:modal.close>
            <flux:button variant="ghost" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
        </flux:modal.close>
        <flux:button wire:click="deleteClient" variant="danger">{{ __('Deactivate Client') }}</flux:button>
    </div>
</flux:modal>
