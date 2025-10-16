<flux:modal
    name="manage-organisation-admins"
    class="max-w-3xl"
    x-on:close="$wire.call('closeManageAdmins')">
    <div class="space-y-6">
    <flux:heading size="lg">{{ __('organisations.manage_admins') }}</flux:heading>

        <div>
            <flux:label class="block text-sm font-medium">{{ __('organisations.manage_admins') }}</flux:label>
            <div class="mt-2 space-y-2">
                @forelse ($organisationAdmins as $admin)
                <div class="flex items-center justify-between gap-3 rounded border p-3">
                    <div>
                        <div class="font-medium">{{ $admin['first_name'] }} {{ $admin['last_name'] }}</div>
                        <div class="text-sm text-gray-500">{{ $admin['username'] }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="outline" wire:click="removeAdmin({{ $admin['id'] }})">{{ __('organisations.remove_admin') }}</flux:button>
                    </div>
                </div>
                @empty
                <div class="text-sm text-gray-500">{{ __('organisations.no_admins') }}</div>
                @endforelse
            </div>
        </div>

        <div>
            <flux:heading size="sm">{{ __('organisations.create_admin') }}</flux:heading>
            <flux:text class="text-sm text-gray-500">{{ __('organisations.create_admin') }}</flux:text>
            {{-- in a follow-up we could add a select to pick users; for now you can call addExistingUserAsAdmin(userId) from another place --}}
        </div>

        <div>
            <flux:heading size="sm">{{ __('organisations.create_admin') }}</flux:heading>
            <form wire:submit.prevent="createAdminForOrganisation" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <flux:input id="new-admin-first" wire:model.defer="newAdminFirstName" :label="__('user.first_name')" required />
                        @error('newAdminFirstName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <flux:input id="new-admin-last" wire:model.defer="newAdminLastName" :label="__('user.last_name')" />
                        @error('newAdminLastName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <flux:input id="new-admin-username" wire:model.defer="newAdminUsername" :label="__('user.username')" required />
                        @error('newAdminUsername') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <flux:input id="new-admin-password" type="password" wire:model.defer="newAdminPassword" :label="__('user.password')" required />
                        @error('newAdminPassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <flux:modal.close>
                        <flux:button type="button" variant="outline" wire:click="closeManageAdmins">{{ __('organisations.cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">{{ __('organisations.create_admin') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
