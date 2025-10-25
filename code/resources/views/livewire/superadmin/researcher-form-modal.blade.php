<flux:modal
    name="superadmin-researcher-form"
    class="max-w-3xl">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ $mode === 'edit' ? __('manage-researchers.editResearcher') : __('manage-researchers.addResearcher') }}
        </flux:heading>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <flux:input
                        id="researcher-first-name"
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
                        id="researcher-last-name"
                        type="text"
                        wire:model.defer="form.last_name"
                        :label="__('user.last_name')" />
                    @error('form.last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input
                        id="researcher-username"
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
                        id="researcher-password"
                        type="password"
                        wire:model.defer="form.password"
                        :label="$editingId ? __('user.new_password_optional') : __('user.password')"
                        :required="!$editingId" />
                    @error('form.password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:select
                        id="researcher-language"
                        wire:model.defer="form.language_id"
                        :label="__('Language')"
                        required>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->language_id }}">
                                {{ __("user.language_{$lang->language_code}") !== "user.language_{$lang->language_code}" ? __("user.language_{$lang->language_code}") : $lang->language_name }}
                            </option>
                        @endforeach
                    </flux:select>
                    @error('form.language_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <flux:label for="researcher-active" class="block text-sm font-medium">
                        {{ __('user.account_status') }}
                    </flux:label>
                    <div class="mt-2">
                        <flux:checkbox
                            id="researcher-active"
                            wire:model.defer="form.active"
                            :label="__('manage-researchers.ResearcherCanSignIn')" />
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
                    {{ $editingId ? __('Save') : __('manage-researchers.createResearcher') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
