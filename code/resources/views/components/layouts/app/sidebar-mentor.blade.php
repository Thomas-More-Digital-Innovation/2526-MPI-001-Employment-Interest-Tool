<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Mentor specific navigation -->
    <flux:navlist.group :heading="__('Mentor Specific SideBar')" class="grid">
        <flux:navlist.item icon="home" :href="route('mentor.example')" :current="request()->routeIs('mentor.example')" wire:navigate>{{ __('Example') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('mentor.client-management')" :current="request()->routeIs('mentor.client-management')" wire:navigate>{{ __('Manage Clients') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>