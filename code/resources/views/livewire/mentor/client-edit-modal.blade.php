<!-- Edit Client Modal -->
<flux:modal name="edit-client-{{ $clientId }}" :show="$showModal" focusable class="max-w-2xl">
    <form wire:submit="updateClient" class="space-y-6">
        <div class="p-6">
            <flux:heading size="lg">{{ __('Edit Client') }}</flux:heading>
            <flux:subheading>{{ __('Update client information') }}</flux:subheading>

            <div class="mt-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>{{ __('First Name') }} *</flux:label>
                        <flux:input wire:model="first_name" />
                        <flux:error name="first_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Last Name') }} *</flux:label>
                        <flux:input wire:model="last_name" />
                        <flux:error name="last_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Username') }} *</flux:label>
                        <flux:input wire:model="username" />
                        <flux:error name="username" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email') }}</flux:label>
                        <flux:input wire:model="email" type="email" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('New Password') }}</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="{{ __('Leave empty to keep current password') }}" />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Vision Type') }} *</flux:label>
                        <flux:select wire:model="vision_type">
                            <option value="normal">{{ __('Normal') }}</option>
                            <option value="colorblind">{{ __('Colorblind') }}</option>
                            <option value="low_vision">{{ __('Low Vision') }}</option>
                        </flux:select>
                        <flux:error name="vision_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Organisation') }} *</flux:label>
                        <flux:select wire:model="organisation_id">
                            <option value="">{{ __('Select Organisation') }}</option>
                            @foreach($organisations as $org)
                                <option value="{{ $org->organisation_id }}">{{ $org->organisation_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="organisation_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Language') }} *</flux:label>
                        <flux:select wire:model="language_id">
                            <option value="">{{ __('Select Language') }}</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="language_id" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:checkbox wire:model="is_sound_on">{{ __('Sound Enabled') }}</flux:checkbox>
                    </flux:field>

                    <flux:field>
                        <flux:checkbox wire:model="active">{{ __('Active') }}</flux:checkbox>
                    </flux:field>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2 rtl:space-x-reverse px-6 py-4 bg-gray-50 border-t">
            <flux:modal.close>
                <flux:button variant="ghost" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button wire:click="updateClient" variant="primary">{{ __('Update Client') }}</flux:button>
        </div>
    </form>
</flux:modal>
