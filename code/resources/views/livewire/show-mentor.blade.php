<div class="flex flex-col items-center justify-center py-10">
    <div class="w-80 h-80 rounded-full overflow-hidden ring-4 ring-zinc-300 shadow-lg">
        <img
            src="{{ $mentor->profile_picture_url}}"
            alt="{{ $mentor->username }}"
            class="w-full h-full object-cover block"
        />
    </div>

    <flux:heading class="mt-6 text-2xl font-semibold text-center">
        {{ $mentor->username }}
    </flux:heading>

    <a href="tel:{{ $mentor->phone_number ?? '' }}" class="mt-6">
        <flux:button class="px-6 py-3 text-lg rounded-xl flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2 5a2 2 0 012-2h2.28a2 2 0 011.94 1.45l.7 2.6a2 2 0 01-.5 1.94l-1.2 1.2a16 16 0 006.1 6.1l1.2-1.2a2 2 0 011.94-.5l2.6.7A2 2 0 0121 17.72V20a2 2 0 01-2 2h-.5C9.94 22 2 14.06 2 4.5V4a2 2 0 010-1z"/>
            </svg>
            Contact Mentor
        </flux:button>
    </a>
</div>
