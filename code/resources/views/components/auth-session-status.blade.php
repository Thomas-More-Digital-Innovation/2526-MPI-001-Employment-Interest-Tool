@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 bg-white border border-gray-200 rounded-lg px-4 py-3']) }}>
        {{ $status }}
    </div>
@endif
