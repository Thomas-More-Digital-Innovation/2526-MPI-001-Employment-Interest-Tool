<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('mentor.dashboard')" :current="request()->routeIs('mentor.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Mentor specific navigation -->
    <flux:navlist.group :heading="__('Mentor Specific SideBar')" class="grid">
        <flux:navlist.item icon="home" :href="route('mentor.example')" :current="request()->routeIs('mentor.example')" wire:navigate>{{ __('Example') }}</flux:navlist.item>
        <flux:navlist.item icon="users" :href="route('mentor.clients-manager')" :current="request()->routeIs('mentor.clients-manager')" wire:navigate>{{ __('manage-clients.clientsManager') }}</flux:navlist.item>
        <flux:navlist.item icon="pencil-square" :href="route('staff.test-picker')" :current="request()->routeIs('staff.test-picker')" wire:navigate>{{ __('testOverview.tests') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
