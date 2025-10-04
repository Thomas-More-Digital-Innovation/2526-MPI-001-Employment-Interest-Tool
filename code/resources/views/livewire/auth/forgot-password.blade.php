<div class="flex flex-col gap-6 ">
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your username to receive a password reset link')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <style>
        .forgot-password-form input {
            color: #374151 !important;
        }
    </style>

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6 forgot-password-form">
        <!-- Username -->
        <flux:input
            wire:model="username"
            :label="__('Username')"
            type="text"
            required
            autofocus
            autocomplete="username"
            :placeholder="__('Username')"
        />

        <flux:button variant="primary" type="submit" class="w-full bg-accent-content">{{ __('Send password reset link') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-gray-200">
        <span>{{ __('Or, return to') }}</span>
        <flux:link :href="route('home')" wire:navigate>{{ __('Home') }}</flux:link>
    </div>
</div>
