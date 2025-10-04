<div class="flex flex-col gap-6 m-5">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your username and password below to log in')"/>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <style>
        .login-form [data-flux-label]{
            color: white !important;
        }
    </style>

    <form method="POST" wire:submit="login" class="flex flex-col gap-6 login-form">
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

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 text-xs end-0 text-white" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
</div>
