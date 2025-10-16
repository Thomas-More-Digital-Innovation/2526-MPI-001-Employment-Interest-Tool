<div class="flex flex-col items-center justify-center py-10">
    <div class="w-[50vw] h-[50vw] md:w-[30vw] md:h-[30vw] rounded-full overflow-hidden ring-4 ring-zinc-300 shadow-lg">
        <img
            src="{{ $mentor->profile_picture_url}}"
            alt="{{ $mentor->username }}"
            class="w-full h-full object-cover block"
        />
    </div>

    <flux:heading class="mt-6 text-2xl font-semibold text-center">
        {{ $mentor->first_name }} {{ $mentor->last_name }}
    </flux:heading>
</div>
