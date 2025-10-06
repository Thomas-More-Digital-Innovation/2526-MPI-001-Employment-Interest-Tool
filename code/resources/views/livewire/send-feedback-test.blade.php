{{-- We have to pass the class via the components properties, because $attributes is not supported in livewire components--}}
<div class="{{ $class }}">
    <!-- Question icon when clicked send mail -->
    <flux:modal.trigger name="modal_mail_send">
        <flux:icon.question-mark-circle class="h-full w-full" wire:click="sendMail"/>
    </flux:modal.trigger>
    <!-- Modal tonen met status mail -->
    <flux:modal x-on:close="$wire.call('closeModal')" name="modal_mail_send" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{$type}}</flux:heading>
                <flux:text class="mt-2">{{$message}}</flux:text>
            </div>
        </div>
    </flux:modal>
</div>
