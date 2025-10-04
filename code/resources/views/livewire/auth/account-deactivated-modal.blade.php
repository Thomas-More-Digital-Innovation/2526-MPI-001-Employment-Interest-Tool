<flux:modal name="account-deactivated" :show="$showModal" focusable class="max-w-md">
    <div class="p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <div>
                <flux:heading size="lg">{{ __('Account Deactivated') }}</flux:heading>
                <flux:subheading>{{ __('Your account is currently inactive') }}</flux:subheading>
            </div>
        </div>

        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                {{ __('Your account has been deactivated by your mentor or administrator. You cannot access the system at this time.') }}
            </p>

            <p class="text-sm text-gray-600">
                {{ __('Please contact your mentor or administrator to reactivate your account if you believe this is an error.') }}
            </p>
        </div>
    </div>

    <div class="flex justify-end px-6 py-4 bg-gray-50 border-t">
        <flux:modal.close>
            <flux:button variant="primary" wire:click="closeModal">{{ __('I Understand') }}</flux:button>
        </flux:modal.close>
    </div>
</flux:modal>
