<div>
    <!-- Top: two cards -->
    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <!-- Client info card -->
        <div style="flex:1; border:1px solid #ddd; padding:16px; border-radius:8px;">
            <h2 style="margin-top:0">Client information</h2>
                <div class="space-y-2">
                    <div><strong>Name:</strong> {{ $clientInfo->first_name ?? '—' }}</div>
                    <div><strong>Lastname:</strong> {{ $clientInfo->last_name ?? '—' }}</div>
                    <div><strong>Email:</strong> {{ $clientInfo->email ?? '—' }}</div>
                </div>
        </div>

        <!-- Empty placeholder card -->
        <div style="flex:1; border:1px dashed #ccc; padding:16px; border-radius:8px;">
            <h2 style="margin-top:0">Placeholder</h2>
            <p>Reserved for future content.</p>
        </div>
    </div>

    <!-- Bottom: questions table -->
    <div style="border:1px solid #eee; padding:12px; border-radius:6px;">
        <h3 style="margin-top:0">Questions and answers</h3>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Question</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Answer</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Time spent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempt->answers ?? [] as $answer)
                        <tr>
                            <td style="padding:8px; border-bottom:1px solid #f7f7f7;">Question{{ $index++ }}</td>
                            <td style="padding:8px; border-bottom:1px solid #f7f7f7;">
                                @if ($answer->answer)
                                {{"Yes"}}
                                @elseif ($answer->unclear)
                                {{"unclear(check e-mail)"}}
                                @elseif ($answer->answer === null)
                                {{"skipped"}}
                                @else
                                {{"No"}}
                                @endif
                              </td>
                            <td style="padding:8px; border-bottom:1px solid #f7f7f7;">
                                @php
                                    $timeSpent = data_get($answer, 'response_time') ?? data_get($answer, 'duration') ?? null;
                                @endphp
                                @if($timeSpent !== null)
                                    Minutes: {{ gmdate($timeSpent >= 3600 ? 'H:i:s' : 'i:s', (int) $timeSpent) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:8px; text-align:center;">No answers available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>