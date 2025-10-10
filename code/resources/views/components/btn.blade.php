@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $baseClasses = "inline-flex items-center justify-center px-6 py-2 rounded-lg font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition";

    $variants = [
        'primary' => "bg-mpi hover:bg-mpi-500 text-white text-2xl px-6 py-4",
        'secondary' => "bg-zinc-200 text-zinc-800 hover:bg-zinc-300",
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
