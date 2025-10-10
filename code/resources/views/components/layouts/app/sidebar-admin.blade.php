<flux:navlist variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">
        <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist.group>

    <!-- Admin specific navigation -->
    <flux:navlist.group :heading="__('Admin Specific SideBar')" class="grid">
        <flux:navlist.item icon="home" :href="route('admin.feedback')" :current="request()->routeIs('admin.feedback')" wire:navigate>{{ __('pageFeedback.Feedback') }}</flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
