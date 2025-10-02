<div>
    <!-- Question icon when clicked send mail -->
    <flux:modal.trigger name="modal_mail_send">
        <flux:icon.question-mark-circle wire:click="sendMail"/>
    </flux:modal.trigger>
    <!-- Modal tonen met status mail -->
    <flux:modal name="modal_mail_send" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{$type}}</flux:heading>
                <flux:text class="mt-2">{{$message}}</flux:text>
            </div>
        </div>
    </flux:modal>
</div>
