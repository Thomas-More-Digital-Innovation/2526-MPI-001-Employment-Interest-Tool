<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="__('First Name')" type="text" required autofocus autocomplete="first_name" />
            <flux:input wire:model="last_name" :label="__('Last Name')" type="text" required autofocus autocomplete="last_name" />
            <!-- Language selector dropdown -->
            <div>
                <flux:label>{{ __('Language') }}</flux:label>
                <select wire:model="language_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Select a language') }}</option>
                    @foreach($this->languages as $language)
                        <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role display -->
            <div>
                <flux:label>{{ __('Role') }}</flux:label>
                <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-md">
                    @if($this->user && $this->user->roles->count() > 0)
                        @foreach($this->user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($role->role === 'SuperAdmin') bg-red-100 text-red-800
                                @elseif($role->role === 'Admin') bg-blue-100 text-blue-800
                                @elseif($role->role === 'Mentor') bg-green-100 text-green-800
                                @elseif($role->role === 'Client') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif
                                @if(!$loop->last) mr-2 @endif">
                                {{ $role->role }}
                            </span>
                        @endforeach
                    @else
                        <span class="text-gray-500 text-sm">{{ __('No role assigned') }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
