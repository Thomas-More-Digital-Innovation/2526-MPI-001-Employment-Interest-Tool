<flux:modal
    name="mentor-client-form"
    class="max-w-3xl">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ $mode === 'edit' ? __('manage-clients.EditClient') : __('manage-clients.AddClient') }}
        </flux:heading>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <flux:input
                        id="client-first-name"
                        type="text"
                        wire:model.defer="form.first_name"
                        :label="__('user.first_name')"
                        required
                        autofocus />
                    @error('form.first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input
                        id="client-last-name"
                        type="text"
                        wire:model.defer="form.last_name"
                        required
                        :label="__('user.last_name')" />
                    @error('form.last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input
                        id="client-username"
                        type="text"
                        wire:model.defer="form.username"
                        :label="__('user.username')"
                        required />
                    @error('form.username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input
                        id="client-password"
                        type="password"
                        wire:model.defer="form.password"
                        :label="$editingId ? __('user.new_password_optional') : __('user.password')"
                        :required="!$editingId" />
                    @error('form.password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:checkbox
                        id="client-sound"
                        wire:model.defer="form.is_sound_on"
                        :label="$form['is_sound_on'] ? __('user.sound_on') : __('user.sound_off')" />
                    @error('form.is_sound_on')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:select
                        id="client-vision-type"
                        wire:model.defer="form.vision_type"
                        :label="__('user.vision_type')"
                        required>
                        @foreach ($visionTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    @error('form.vision_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:select
                        id="client-language"
                        wire:model.defer="form.language_id"
                        :label="__('Language')"
                        required>
                        @foreach ($languages as $language)
                        <option value="{{ $language['id'] }}" {{ $language['id'] === $form['language_id'] ? 'selected' : '' }}>
                            {{ $language['label'] }}
                        </option>
                        @endforeach
                    </flux:select>
                    @error('form.language_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:label for="client-active" class="block text-sm font-medium">
                        {{ __('user.account_status') }}
                    </flux:label>
                    <div class="mt-2">
                        <flux:checkbox
                            id="client-active"
                            wire:model.defer="form.active"
                            :label="__('manage-clients.ClientCanSignIn')" />
                    </div>
                    @error('form.active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 md:flex-row md:items-center md:justify-end">
                <flux:button
                    type="button"
                    variant="outline"
                    wire:click="cancel">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary">
                    {{ $editingId ? __('manage-clients.SaveChanges') : __('manage-clients.CreateClient') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
