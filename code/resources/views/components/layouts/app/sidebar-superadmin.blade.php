<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('superadmin.dashboard')" :current="request()->routeIs('superadmin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- SuperAdmin specific navigation -->
    <flux:navlist.group :heading="__('SuperAdmin Specific SideBar')" class="grid">
        <flux:navlist.item icon="users" :href="route('superadmin.interest-field-manager')" :current="request()->routeIs('superadmin.interest-field-manager')" wire:navigate>{{ __('Interest Field Manager') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('superadmin.manage-researchers')" :current="request()->routeIs('superadmin.manage-researchers')" wire:navigate>{{ __('manage-researchers.manage_researchers') }}</flux:navlist.item>
        <flux:navlist.item icon="building" :href="route('superadmin.organisations-manager')" :current="request()->routeIs('superadmin.organisations-manager')" wire:navigate>{{ __('organisations.sidebar') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
