<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Admin specific navigation -->
    <flux:navlist.group :heading="__('Admin Specific SideBar')" class="grid">
        <flux:navlist.item icon="home" :href="route('admin.example')" :current="request()->routeIs('admin.example')" wire:navigate>{{ __('Example') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('admin.admin-clients-manager')" :current="request()->routeIs('admin.admin-clients-manager')" wire:navigate>{{ __('Manage Clients') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
