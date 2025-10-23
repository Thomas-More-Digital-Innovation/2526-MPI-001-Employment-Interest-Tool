<flux:modal
    name="admin-client-form"
    class="max-w-3xl"
    x-on:close="$wire.call('cancel')">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ $mode === 'edit' ? __('manage-clients.EditClient') : __('manage-clients.AddClient') }}
        </flux:heading>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <flux:input
                        id="admin-client-first-name"
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
                        id="admin-client-last-name"
                        type="text"
                        wire:model.defer="form.last_name"
                        :label="__('user.last_name')" />
                    @error('form.last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input
                        id="admin-client-username"
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
                        id="admin-client-password"
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
                        id="admin-client-sound"
                        wire:model.defer="form.is_sound_on"
                        :label="$form['is_sound_on'] ? __('user.sound_on') : __('user.sound_off')" />
                    @error('form.is_sound_on')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:select
                        id="admin-client-mentor"
                        wire:model.defer="form.mentor_id"
                        :label="__('Mentor')"
                        required>
                        @if ($mode !== "edit")
                        <option value="">{{ __('manage-clients.selectMentor') }}</option>
                        @endif
                        @foreach ($mentorOptions as $mentor)
                        <option value="{{ $mentor['id'] }}" @selected((string) $mentor['id'] === (string) $form['mentor_id'])>
                            {{ $mentor['label'] }}
                        </option>
                        @endforeach
                    </flux:select>
                    @error('form.mentor_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:select
                        id="admin-client-vision-type"
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
                        id="admin-client-language"
                        wire:model.defer="form.language_id"
                        :label="__('Language')"
                        required>
                        @foreach ($languages as $language)
                        <option value="{{ $language['id'] }}">
                            {{ $language['label'] }}
                        </option>
                        @endforeach
                    </flux:select>
                    @error('form.language_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:checkbox
                        id="admin-client-active"
                        wire:model.defer="form.active"
                        :label="$form['active'] ? __('user.active') : __('user.inactive')" />
                    @error('form.active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                @if ($mode === 'edit' && !$form['active'])
                <flux:button 
                    type="button" 
                    variant="danger"
                    wire:click="requestDelete">
                    {{ __('manage-clients.DeleteClient') }}
                </flux:button>
                @else
                <div></div>
                @endif

                <div class="flex space-x-2 rtl:space-x-reverse">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">
                            {{ __('Cancel') }}
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" class="bg-color-mpi">
                        {{ $mode === 'edit' ? __('manage-clients.UpdateClient') : __('manage-clients.AddClient') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>
