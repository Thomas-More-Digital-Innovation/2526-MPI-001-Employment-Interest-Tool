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
