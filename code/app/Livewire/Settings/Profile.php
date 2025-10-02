<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $username = '';
    public string $language_id = '';
    public $user;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->user = Auth::user()->load('roles');
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->username = $this->user->username;
        $this->language_id = $this->user->language_id;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class)->ignore($user->user_id, 'user_id'),
            ],
            'language_id' => ['required', 'exists:language,language_id'],
        ]);

        $user->fill($validated);
        $user->save();

        $this->dispatch('profile-updated', username: $user->username);
    }

    /**
     * Get all available languages for the dropdown.
     */
    public function getLanguagesProperty()
    {
        return Language::all();
    }

    // Email verification is not used in this application anymore.
}
