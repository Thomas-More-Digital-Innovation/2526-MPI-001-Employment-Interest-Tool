<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profiel')" :subheading="__('Wijzig uw profielinstellingen')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="first_name" :label="__('Voornaam')" type="text" required autofocus autocomplete="voornaam" />
            <flux:input wire:model="last_name" :label="__('Achternaam')" type="text" required autofocus autocomplete="achternaam" />
            <flux:checkbox wire:model="is_sound_on" :label="$is_sound_on ? __('Geluid Aan') : __('Geluid Uit')" type="checkbox" required autofocus/>
            {{-- Dropdown with type of vision--}}
            <flux:select wire:model="vision_type" :label="__('Visie type')">
                <option value="Normal">Normaal</option>
                <option value="Deuteranopia">Deuteranopia</option>
                <option value="Protanopia">Protanopia</option>
                <option value="Tritanopia">Tritanopia</option>
            </flux:select>

            <!-- Language selector dropdown -->
            <div>
                <flux:label>{{ __('Taal') }}</flux:label>
                <flux:select wire:model="language_id" required>
                    @foreach($this->languages as $language)
                        <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Profile picture selection -->
            <div class="flex flex-col space-y-2">
                <flux:label>{{__('Profielfoto')}}</flux:label>

                @if (!empty(Auth::user()->profile_picture_url))
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture_url) }}"
                         alt="Profielfoto"
                         class="w-24 h-24 rounded-full">
                @else
                    <div class="w-24 h-24 my-4 rounded-full bg-gray-200 flex items-center justify-center">
                        <flux:icon.user class="w-12 h-12 text-gray-500" />
                    </div>
                @endif

                <div>
                    <label for="profile_picture"
                           class="px-4 py-2 border rounded-md shadow-sm text-sm text-gray-700 bg-white cursor-pointer hover:bg-gray-100">
                        {{ __('Kies bestand') }}
                    </label>
                    <input id="profile_picture"
                           type="file"
                           wire:model="profile_picture"
                           accept="image/*"
                           class="hidden">
                </div>

                @error('profile_picture')
                    <span class="text-red-600 test-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Opslaan') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Opgeslagen.') }}
                </x-action-message>
            </div>
        </form>

{{--We don't allow the user to delete his account--}}
{{--        <livewire:settings.delete-user-form />--}}
    </x-settings.layout>
</section>
