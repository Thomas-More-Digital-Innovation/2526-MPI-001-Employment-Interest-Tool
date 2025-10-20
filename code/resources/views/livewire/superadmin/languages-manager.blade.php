<div
    x-data="{}"
    x-on:modal-open.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).show()"
    x-on:modal-close.window="$event.detail && $event.detail.name && $flux.modal($event.detail.name).close()"
    class="space-y-6">

    @if (session('status'))
        @php
            $statusType = session('status')['type'] ?? 'success';
            $statusMessage = session('status')['message'] ?? '';
        @endphp
        <div class="rounded-md p-4 text-sm {{ $statusType === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-50 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
            {{ $statusMessage }}
        </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-end-safe md:justify-between">
        <div class="flex-1">
            <flux:label class="block text-sm font-medium" for="language-search">
                {{ __('Search') }}
            </flux:label>
            <flux:input
                id="language-search"
                type="search"
                icon="magnifying-glass"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('languages.search') }}" />
        </div>
    </div>

    @include('livewire.superadmin.languages-manager-table', ['records' => $records])

</div>
