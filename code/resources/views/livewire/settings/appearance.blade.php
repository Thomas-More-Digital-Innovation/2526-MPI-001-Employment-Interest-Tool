<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Systeem')" :subheading=" __('Wijzig uw systeeminstellingen')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Licht') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Donker') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('Systeem') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
