<div>
    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #ddd; padding:16px; border-radius:8px;">
            <h2 style="margin-top:0"><strong>Client information:</strong></h2>
                <div class="space-y-2">
                    <div><strong>Name:</strong> {{ $clientInfo->first_name ?? '—' }}</div>
                    <div><strong>Lastname:</strong> {{ $clientInfo->last_name ?? '—' }}</div>
                    <div><strong>Email:</strong> {{ $clientInfo->email ?? '—' }}</div>
                </div>
        </div>

        <div class="w-3/4 mx-auto overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white shadow-sm " >
            <h2 style="margin-top:0">Results</h2>
            @php
                // Build labels and data from the attempt answers.
                $labels = $graphLabels;
                $data = $graphData;
            @endphp

            {{-- Insert Livewire Chart component and pass precomputed labels/data --}}
            <div>
                <livewire:chart :labels="$labels" :data="$data" />
            </div>
        </div>
    </div>


    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white shadow-sm mt-6">
        <h3 class="px-4 pt-4 text-lg font-semibold">Questions and answers</h3>
        <x-table class="min-w-full divide-y divide-gray-800" border>
            <thead class="bg-gray-50 dark:bg-zinc-600">
                <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <th class="px-4 py-3">Question</th>
                    <th class="px-4 py-3">Time spent</th>
                    <th class="px-4 py-3">Answer</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm text-gray-700 dark:bg-zinc-500 dark:text-gray-50">
                @php $qIndex = 1; @endphp
                @forelse($attempt->answers ?? [] as $answer)
                    <tr>
                        <td class="px-4 py-3">
                            Question {{ $qIndex }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $timeSpent = data_get($answer, 'response_time') ?? data_get($answer, 'duration') ?? null;
                            @endphp
                            @if($timeSpent !== null)
                            Minutes: {{ gmdate($timeSpent >= 3600 ? 'H:i:s' : 'i:s', (int) $timeSpent) }}
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($answer->answer)
                                {{ __('Like') }}
                                <span class="inline-block align-middle ml-2">
                                    <flux:icon.check-circle class="text-green-500 w-5 h-5" />
                                </span>
                            @elseif ($answer->unclear)
                                {{ __('Unclear') }}
                                <span class="inline-block align-middle ml-2">
                                    <flux:icon.question-mark-circle class="text-amber-500 w-5 h-5" />
                                </span>
                            @elseif ($answer->answer === null)
                                {{ __('Skipped') }}
                                <span class="inline-block align-middle ml-2">
                                    <flux:icon.question-mark-circle class="text-amber-500 w-5 h-5" />
                                </span>
                            @else
                                {{ __('Dislike') }}
                                <span class="inline-block align-middle ml-2">
                                    <flux:icon.x-circle class="text-red-500 w-5 h-5" />
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    type="button"
                                    size="sm"
                                    class="bg-color text-amber-50"
                                    wire:click="toggleRow({{ $qIndex }})"
                                    aria-pressed="{{ $openRow === $qIndex ? 'true' : 'false' }}"
                                >
                                    {{ $openRow === $qIndex ? __('Hide details') : __('Show details') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    <tr @if($openRow === $qIndex) @else style="display:none;" @endif>
                        <td colspan="4" class="px-6 py-4 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 border-t">
                            <div>
                                <strong>Question text:</strong> {{ $answer->question->question ?? '—' }}<br>
                                <strong>Image:</strong>  <img src="{{ $answer->question->getImageUrl($currentLocale) }}" class="w-full h-64 object-cover rounded-t-2xl" alt="{{ $answer->question->getImageDescription($currentLocale) }}" /><br>
                            </div>
                        </td>
                    </tr>
                    @php $qIndex++; @endphp
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('No answers available.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
</div>
