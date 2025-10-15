<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('superadmin.dashboard')" :current="request()->routeIs('superadmin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- SuperAdmin specific navigation -->
    <flux:navlist.group :heading="__('SuperAdmin Specific SideBar')" class="grid">
        <flux:navlist.item icon="users" :href="route('superadmin.system')" :current="request()->routeIs('superadmin.system')" wire:navigate>{{ __('SuperAdmin System') }}</flux:navlist.item>

        <flux:navlist.item icon="users" :href="route('superadmin.test.create')" :current="request()->routeIs('superadmin.test.create')" wire:navigate>{{ __('Test Creation') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('superadmin.test.manager')" :current="request()->routeIs('superadmin.test.manager')" wire:navigate>{{ __('Test Manage') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('superadmin.interest-field-manager')" :current="request()->routeIs('superadmin.interest-field-manager')" wire:navigate>{{ __('Interest Field Manager') }}</flux:navlist.item>

    </flux:navlist.group>
</flux:navlist>
