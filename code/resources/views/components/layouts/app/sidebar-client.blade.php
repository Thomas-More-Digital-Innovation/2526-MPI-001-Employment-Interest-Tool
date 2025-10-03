<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Client specific navigation -->
    <flux:navlist.group :heading="__('Client Specific SideBar')" class="grid">
        <flux:navlist.item icon="home" :href="route('client.example')" :current="request()->routeIs('client.example')" wire:navigate>{{ __('Example') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>