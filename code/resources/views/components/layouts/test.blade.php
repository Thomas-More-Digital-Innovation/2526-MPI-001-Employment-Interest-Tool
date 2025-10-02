<x-layouts.app.nonavigation :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.nonavigation>
