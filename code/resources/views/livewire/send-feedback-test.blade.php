{{-- We have to pass the class via the components properties, because $attributes is not supported in livewire components--}}
<div class="{{ $class }}">
    <!-- Question icon when clicked send mail -->
    <flux:modal.trigger name="modal_mail_send">
        <flux:icon.question-mark-circle class="h-full w-full" wire:click="sendMail"/>
    </flux:modal.trigger>
    <!-- Modal tonen met status mail -->
    <flux:modal x-on:close="$wire.call('closeModal')" name="modal_mail_send" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <!-- Spinner inside modal while sending -->
                <div class="hidden mx-auto w-12 h-12" wire:loading.class.remove="hidden" wire:target="sendMail" aria-hidden="true">
                    <flux:icon.loading class="h-12 w-12 text-gray-600" />
                </div>

                <!-- Heading: show loading text while sending, otherwise show result type -->
                <flux:heading size="lg">
                    @if (empty($type))
                        <span wire:loading wire:target="sendMail">Bezig met verzenden...</span>
                    @endif
                    <span wire:loading.remove wire:target="sendMail">{{ $type }}</span>
                </flux:heading>

                <!-- Message: show loading message while sending, otherwise show result message -->
                <flux:text class="mt-2">
                    @if (empty($type))
                        <span wire:loading wire:target="sendMail">Uw mail wordt verzonden, even geduld...</span>
                    @endif
                    <span wire:loading.remove wire:target="sendMail">{{ $message }}</span>
                </flux:text>
            </div>
        </div>
    </flux:modal>
</div>
