<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('researcher.dashboard')" :current="request()->routeIs('researcher.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Client specific navigation -->
    <flux:navlist.group :heading="__('Researcher')" class="grid">
        <flux:navlist.item icon="home" :href="route('researcher.example')" :current="request()->routeIs('researcher.example')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
