<div class="space-y-6 bg-white dark:bg-gray-700 ">
    @if (session('status'))
    <div class="rounded-md bg-green-50 p-4 text-green-800">
        {{ session('status') }}
    </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700" for="client-search">
                {{ __('Search clients') }}
            </label>
            <input
                id="client-search"
                type="search"
                wire:model.live.debounce.400ms="search"
                class="mt-1 w-full rounded-md border border-gray-300 bg-white dark:bg-gray-900 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring"
                placeholder="{{ __('Search by name or username') }}" />
        </div>
        <div class="flex-shrink-0">
            <button
                type="button"
                wire:click="startCreate"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Add client') }}
            </button>
        </div>
    </div>

    @include('livewire.mentor.clients-manager-table', ['records' => $records])

    <div class="flex flex-wrap items-center gap-3">
        <button
            type="button"
            wire:click="toggleShowInactivated"
            class="inline-flex items-center rounded-md border {{ $showInactivated ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700' }} px-3 py-1 text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <flux:icon name="{{ $showInactivated ? 'eye-slash' : 'eye' }}" class="w-5 h-5" />
            &nbsp;
            {{ $showInactivated ? __('Hide inactivated clients') :  __('Show inactivated clients') }}
        </button>
    </div>
    @if ($showInactivated)
        <div class="mt-4">
            @include('livewire.mentor.clients-manager-table', ['records' => $inactivatedClients])
        </div>
    @endif

    @if ($formModalVisible)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:keydown.escape="closeFormModal">
        <div class="relative w-full max-w-3xl rounded-lg bg-white shadow-xl">
            <button
                type="button"
                wire:click="closeFormModal"
                class="absolute right-4 top-4 text-gray-400 hover:text-gray-600"
                aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $formModalMode === 'edit' ? __('Edit client') : __('Add client') }}
                </h2>
            </div>

            <form wire:submit.prevent="save" class="px-6 py-5 space-y-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="client-first-name">
                            {{ __('First name') }}
                        </label>
                        <input
                            id="client-first-name"
                            type="text"
                            wire:model.defer="form.first_name"
                            class="mt-1 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring"
                            required />
                        @error('form.first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="client-last-name">
                            {{ __('Last name (optional)') }}
                        </label>
                        <input
                            id="client-last-name"
                            type="text"
                            wire:model.defer="form.last_name"
                            class="mt-1 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring" />
                        @error('form.last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="client-username">
                            {{ __('Username') }}
                        </label>
                        <input
                            id="client-username"
                            type="text"
                            wire:model.defer="form.username"
                            class="mt-1 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring"
                            required />
                        @error('form.username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="client-password">
                            {{ $editingId ? __('New password (leave blank to keep current)') : __('Password') }}
                        </label>
                        <input
                            id="client-password"
                            type="password"
                            wire:model.defer="form.password"
                            class="mt-1 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring"
                            @if (!$editingId) required @endif />
                        @error('form.password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="client-language">
                            {{ __('Language') }}
                        </label>
                        <select
                            id="client-language"
                            wire:model.defer="form.language_id"
                            class="mt-1 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring"
                            required>
                            <option value="">{{ __('Select a language') }}</option>
                            @foreach ($languages as $language)
                            <option value="{{ $language['id'] }}">
                                {{ $language['label'] }}
                            </option>
                            @endforeach
                        </select>
                        @error('form.language_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Active') }}
                        </label>
                        <div class="mt-2 flex items-center gap-3">
                        {{-- <button
                            type="button"
                            wire:click="requestToggle({{ $client->user_id }})"
                            class="inline-flex items-center rounded-md {{ $client->active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} px-3 py-1 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ $client->active ? __('Disable') : __('Enable') }}
                        </button>--}}
                            <input
                                id="client-active"
                                type="checkbox"
                                wire:model.defer="form.active"
                                class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <label for="client-active" class="text-sm text-gray-700">
                                {{ __('Client can sign in') }}
                            </label>
                        </div>
                        @error('form.active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <span class="block text-sm font-medium text-gray-700">
                        {{ __('Disabilities (optional)') }}
                    </span>
                    <div class="mt-2 grid gap-2 md:grid-cols-2">
                        @forelse ($disabilityOptions as $option)
                        <label class="flex items-center gap-3 rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm">
                            <input
                                type="checkbox"
                                value="{{ $option['id'] }}"
                                wire:model.defer="form.disability_ids"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-sm text-gray-700">{{ $option['label'] }}</span>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500">
                            {{ __('No disability options available yet.') }}
                        </p>
                        @endforelse
                    </div>
                    @error('form.disability_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('form.disability_ids.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 md:flex-row md:items-center md:justify-end">
                    <button
                        type="button"
                        wire:click="cancelForm"
                        class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ $editingId ? __('Save changes') : __('Create client') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($toggleModalVisible)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:keydown.escape="closeToggleModal">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $toggleModalWillActivate ? __('Enable client') : __('Disable client') }}
                </h2>
            </div>

            <div class="space-y-4 px-6 py-5 text-sm text-gray-700">
                <p>
                    {{ $toggleModalWillActivate
                            ? __('Are you sure you want to enable :client? They will regain access immediately.', ['client' => $toggleModalName])
                            : __('Are you sure you want to disable :client? They will lose access until re-enabled.', ['client' => $toggleModalName]) }}
                </p>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button
                    type="button"
                    wire:click="closeToggleModal"
                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ __('Cancel') }}
                </button>
                <button
                    type="button"
                    wire:click="confirmToggle"
                    class="inline-flex items-center rounded-md {{ $toggleModalWillActivate ? 'bg-green-600 hover:bg-green-700' : 'bg-yellow-600 hover:bg-yellow-700' }} px-4 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $toggleModalWillActivate ? 'focus:ring-green-500' : 'focus:ring-yellow-500' }}">
                    {{ $toggleModalWillActivate ? __('Enable client') : __('Disable client') }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
