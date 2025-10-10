{{-- resources/views/livewire/mentor/client-tests.blade.php --}}
@php /** @var \Illuminate\Contracts\Support\Arrayable|\Illuminate\Support\Collection $attempt */ @endphp
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white shadow-sm">
    <x-table class="min-w-full divide-y divide-gray-800" border>
        <thead class="bg-gray-50 dark:bg-zinc-600">
            <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">{{ __('Test name') }}</th>
                <th class="px-4 py-3">{{ __('Status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-500 dark:text-gray-50">
            @forelse ($attempts as $attempt)
            <tr wire:key="test-row-{{ $attempt->testattempt_id}}" class="hover:bg-gray-50 hover:dark:bg-zinc-600">
                <td class="px-4 py-3">{{ $attempt->attempt_id}}</td>
                <td class="px-4 py-3">{{ $attempt->name ?? data_get($attempt, 'attempt.test.name', '—') }}</td>
                <td class="px-4 py-3">
                    @if(optional($attempt)->finished)
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-green-100 text-green-800">{{ __('Completed') }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800">{{ __('Pending') }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <flux:button
                            type="button"
                            size="sm"
                            icon="eye"
                            class="bg-color-mpi text-amber-50"
                            wire:click="viewTestResults({{ $attempt->id }})">
                            {{ __('View results') }}
                        </flux:button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                    {{ __('No tests found.') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </x-table>
</div>