<!-- open modal -->
<div x-data="{ open: @entangle('open') }" x-show="open"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

    <!-- Modal can not be closed-->
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm"
         @click.outside.stop @keydown.escape.prevent>

        <h2 class="text-lg font-semibold mb-4">Stel je wachtwoord in</h2>
        <!-- Show form -->
        <form wire:submit.prevent="savePassword" class="space-y-3">
            <input type="password"
                   wire:model.defer="password"
                   placeholder="Nieuw wachtwoord"
                   class="w-full border p-2 rounded" required>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <input type="password"
                   wire:model.defer="password_confirmation"
                   placeholder="Bevestig wachtwoord"
                   class="w-full border p-2 rounded" required>

            <button type="submit"
                    class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Opslaan
            </button>
        </form>

    </div>
</div>
