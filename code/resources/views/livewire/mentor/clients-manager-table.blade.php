@props([
    'records' => [],
])

<div class="overflow-x-scroll rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr class="text-left text-sm font-semibold text-gray-700">
                <th class="px-4 py-3">{{ __('First name') }}</th>
                <th class="px-4 py-3">{{ __('Last name') }}</th>
                <th class="px-4 py-3">{{ __('Username') }}</th>
                <th class="px-4 py-3">{{ __('Language') }}</th>
                <th class="px-4 py-3">{{ __('Disabilities') }}</th>
                <th class="px-4 py-3">{{ __('Status') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
            @forelse ($records as $client)
            <tr wire:key="client-{{ $client->user_id }}" class="hover:bg-gray-50">
                <td class="px-4 py-3">{{ $client->first_name }}</td>
                <td class="px-4 py-3">{{ $client->last_name }}</td>
                <td class="px-4 py-3 font-mono">{{ $client->username }}</td>
                <td class="px-4 py-3">{{ optional($client->language)->language_name }}</td>
                <td class="px-4 py-3">
                    @if ($client->options->isEmpty())
                    <span class="text-gray-400">{{ __('None') }}</span>
                    @else
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($client->options as $option)
                        <li>{{ $option->option_name }}</li>
                        @endforeach
                    </ul>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $client->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $client->active ? __('Active') : __('Disabled') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="startEdit({{ $client->user_id }})"
                            class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Edit') }}
                        </button>
                        <button
                            type="button"
                            wire:click="requestToggle({{ $client->user_id }})"
                            class="inline-flex items-center rounded-md {{ $client->active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} px-3 py-1 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ $client->active ? __('Disable') : __('Enable') }}
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                    {{ __('No clients found yet.') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
        {{ $records->links() }}
    </div>
</div>
