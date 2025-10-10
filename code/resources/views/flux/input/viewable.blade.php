@pure

@php
$inputSize = $size ?? 'md';

$normalizedSize = match ($inputSize) {
    null, '', 'base', 'default' => 'md',
    default => $inputSize,
};

$buttonSizeMap = [
    'xs' => 'xs',
    'sm' => 'xs',
    'md' => 'sm',
    'lg' => 'base',
    'xl' => 'base',
];

$buttonSize = $buttonSizeMap[$normalizedSize] ?? 'sm';

$attributes = $attributes->merge([
    'variant' => 'subtle',
    'class' => '-me-1',
    'square' => true,
    'size' => null,
]);
@endphp

<flux:button
    :$attributes
    :size="$buttonSize"
    x-data="{ open: false }"
    x-on:click="open = ! open; $el.closest('[data-flux-input]').querySelector('input').setAttribute('type', open ? 'text' : 'password')"
    x-bind:data-viewable-open="open"
    aria-label="{{ __('Toggle password visibility') }}"

    {{-- We need to make the input type "durable" (immune to Livewire morph manipulations): --}}
    x-init="
        let input = $el.closest('[data-flux-input]')?.querySelector('input');

        if (! input) return;

        let observer = new MutationObserver(() => {
            let type = open ? 'text' : 'password';
            if (input.getAttribute('type') === type) return;
            input.setAttribute('type', type)
        });

        observer.observe(input, { attributes: true, attributeFilter: ['type'] });
    "
>
    <flux:icon.eye-slash variant="micro" class="hidden [[data-viewable-open]>&]:block" />
    <flux:icon.eye variant="micro" class="block [[data-viewable-open]>&]:hidden" />
</flux:button>
