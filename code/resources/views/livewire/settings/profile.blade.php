<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('user.change_profile_settings')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="ucfirst(__('user.first_name'))" type="text" required autofocus autocomplete="first_name" />
            <flux:input wire:model="last_name" :label="ucfirst(__('user.last_name'))" type="text" required autofocus autocomplete="last_name" />
            <flux:checkbox wire:model="is_sound_on" :label="$is_sound_on ? __('user.sound_on') : __('user.sound_off')" type="checkbox" required autofocus/>
            {{-- Dropdown with type of vision--}}
            <flux:select wire:model="vision_type" :label="__('user.vision_type')" required>
                 <option value="Normal">{{ __('user.vision_type_normal') }}</option>
                 <option value="Deuteranopia">{{ __('user.vision_type_deuteranopia') }}</option>
                 <option value="Protanopia">{{ __('user.vision_type_protanopia') }}</option>
                 <option value="Tritanopia">{{ __('user.vision_type_tritanopia') }}</option>
            </flux:select>

            <!-- Language selector dropdown -->
            <div>
                <flux:label>{{ __('user.language') }}</flux:label>
                <flux:select wire:model="language_id" required>
                    @foreach($this->languages as $language)
                        <option value="{{ $language->language_id }}">
                           {{ __("user.language_{$language->language_code}") !== "user.language_{$language->language_code}" ? __("user.language_{$language->language_code}") : $language->language_name }}
                        </option>
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
    
    @push('scripts')
    <script>
        Livewire.on('reload-page-for-language', () => {
            window.location.reload();
        });
    </script>
    @endpush
