<x-layouts.app.sidebar>
    <flux:main>
        <div class="space-y-6 p-6">
            <div class="dark:bg-white bg-gray-500 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-2xl font-bold tracking-tight dark:text-black text-white">{{ __('pageFeedback.send_feedback') }}</h2>
                    <p class="dark=:text-black text-white">{{__('pageFeedback.feedback_to_superadmin')}}</p>
                    <livewire:admin.feedback-form-admin/>
                </div>
            </div>

        </div>
    </flux:main>
</x-layouts.app.sidebar>
