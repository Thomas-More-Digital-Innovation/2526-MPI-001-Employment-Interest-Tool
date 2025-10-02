<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- SuperAdmin specific navigation -->
    <flux:navlist.group :heading="__('SuperAdmin Specific SideBar')" class="grid">
        <flux:navlist.item icon="users" :href="route('superadmin.system')" :current="request()->routeIs('superadmin.system')" wire:navigate>{{ __('SuperAdmin System') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>