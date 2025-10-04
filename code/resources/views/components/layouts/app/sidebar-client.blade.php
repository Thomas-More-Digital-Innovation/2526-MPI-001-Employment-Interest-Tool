<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Client specific navigation -->
    <flux:navlist.group :heading="__('Client')" class="grid">
        <flux:navlist.item icon="home" :href="route('client.example')" :current="request()->routeIs('client.example')" wire:navigate>{{ __('general.take_test') }}</flux:navlist.item>
        <flux:navlist.item icon="pencil" :href="route('client.taketest')" :current="request()->routeIs('client.taketest')" wire:navigate>{{ __('general.take_test') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
