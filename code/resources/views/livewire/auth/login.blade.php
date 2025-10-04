<div>
    <div class="flex flex-col gap-6 m-5">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your username and password below to log in')"/>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" wire:submit="login" class="flex flex-col gap-6">
            <!-- Username -->
            <flux:field>
                <flux:label>{{ __('Username') }}</flux:label>
                <flux:input
                    wire:model="username"
                    type="text"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Username"
                />
            </flux:field>

            <!-- Password -->
            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <div class="relative">
                    <flux:input
                        wire:model="password"
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
            </flux:field>

            <!-- Error Display (only if there are errors) -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="text-sm text-red-800">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
