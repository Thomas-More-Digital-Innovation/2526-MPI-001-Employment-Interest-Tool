@props(['position' => 'top-right'])

<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        @click.away="open = false"
        class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md"
    >
        <flux:icon.language class="w-4 h-4 text-white" />
        <span class="text-white mr-1">{{ __('Language') }}</span>
        <flux:icon.chevron-down class="w-4 h-4 text-white" />
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute {{ $position === 'top-left' ? 'left-0' : 'right-0' }} z-50 mt-2 w-40 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1">
            <a
                href="{{ route('locale.change', 'en') }}"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-50 font-semibold' : '' }}"
            >
                {{ __('English') }}
                @if(app()->getLocale() === 'en')
                    <svg class="w-4 h-4 ml-auto text-indigo-600" fill="black" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </a>
            <a
                href="{{ route('locale.change', 'nl') }}"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'nl' ? 'bg-gray-50 font-semibold' : '' }}"
            >
                {{ __('Dutch') }}
                @if(app()->getLocale() === 'nl')
                    <svg class="w-4 h-4 ml-auto text-indigo-600" fill="black" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </a>
        </div>
    </div>
</div>
