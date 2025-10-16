@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading class="text-gray-100" size="xl">{{ $title }}</flux:heading>
    <flux:subheading class="text-gray-200" size="xl">{{ $description }}</flux:subheading>
</div>
