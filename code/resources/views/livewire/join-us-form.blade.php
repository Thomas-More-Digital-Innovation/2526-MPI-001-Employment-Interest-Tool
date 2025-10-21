<div class="bg-neutral-800 rounded-2xl w-1/2 p-10 m-3">
    <flux:button href="{{ route('home') }}" >{{ __('Go Home') }}</flux:button>
    @if (session()->has('message'))
        <div class="mb-4 mt-4 p-4 bg-color-mpi rounded">
            {{ session('message') }}
        </div>
    @endif
    <form wire:submit.prevent="sendMail" class="flex flex-col text-white space-y-3">
        <h1 class="text-3xl text-center">{{ __('Want to join us?') }}</h1>

        <label for="organsiation" class="text-2xl">{{ __('Organization') }}</label>
        <flux:input type="text" size="sm" name="organisation" id="organisation" wire:model.live="organisation" placeholder="Acme Inc." required/>

        <label for="fullName" class="text-2xl">{{ __('Full Name') }}</label>
        <flux:input type="text" size="sm" name="fullName" id="fullName" wire:model.live="fullName" placeholder="John Doe" required/>

        <label for="emailAddress" class="text-2xl">{{ __('Email Address') }}</label>
        <flux:input type="email" size="sm" name="emailAddress" id="emailAddress" wire:model.live="emailAddress" placeholder="amazing@example.com" required/>

        <label for="heardFrom" class="text-2xl">{{ __('Where have you heard about us?') }}</label>
        <flux:input type="text" size="sm" name="heardFrom" id="heardFrom" wire:model.live="heardFrom" />

        <label for="joinUs" class="text-2xl">{{ __('Why would you like to join us?') }}</label>
        <flux:textarea size="sm" name="joinUs" id="joinUs" wire:model.live="joinUs"/>

        <button type="submit" class="bg-color-mpi text-2xl p-2 rounded-2xl duration-200 ease-in-out">{{ __('Submit') }}</button>
    </form>
</div>