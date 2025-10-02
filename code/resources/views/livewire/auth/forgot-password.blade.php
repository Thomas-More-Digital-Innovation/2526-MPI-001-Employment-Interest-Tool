 <div class="flex flex-col gap-6">
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your username to receive a password reset link')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Username -->
        <flux:input
            wire:model="username"
            :label="__('Username')"
            type="text"
            required
            autofocus
            placeholder="username"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Send password reset link') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('Or, return to') }}</span>
        <flux:link :href="route('home')" wire:navigate>{{ __('log in') }}</flux:link>
    </div>
</div>
