<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profiel')" :subheading="__('Wijzig uw profielinstellingen')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="ucfirst(__('validation.attributes.first_name'))" type="text" required autofocus autocomplete="voornaam" />
            <flux:input wire:model="last_name" :label="ucfirst(__('validation.attributes.last_name'))" type="text" required autofocus autocomplete="achternaam" />
            <flux:checkbox wire:model="is_sound_on" :label="$is_sound_on ? __('Geluid Aan') : __('Geluid Uit')" type="checkbox" required autofocus/>
            {{-- Dropdown with type of vision--}}
            <flux:select wire:model="vision_type" :label="__('user.vision_type')" required>
                <option value="Normal">Normaal</option>
                <option value="Deuteranopia">Deuteranopia</option>
                <option value="Protanopia">Protanopia</option>
                <option value="Tritanopia">Tritanopia</option>
            </flux:select>

            <!-- Language selector dropdown -->
            <div>
                <flux:label>{{ __('user.language') }}</flux:label>
                <flux:select wire:model="language_id" required>
                    @foreach($this->languages as $language)
                        <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                    @endforeach
                </flux:select>
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

{{--We don't allow the user to delete his account--}}
{{--        <livewire:settings.delete-user-form />--}}
    </x-settings.layout>
</section>
