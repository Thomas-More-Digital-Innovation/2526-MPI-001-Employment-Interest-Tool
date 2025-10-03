<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $first_name = '';
    public string $last_name = '';
    public string $username = '';
    public bool $is_sound_on= false;
    public string $vision_type ='';
    public string $language_id = '';
    protected string $original_language_id = '';
    public $profile_picture;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
//      $this->user = Auth::user()->load('roles');
    $user = Auth::user();
      $this->first_name = $user->first_name;
      $this->last_name = $user->last_name;
      $this->is_sound_on=$user->is_sound_on;
      $this->vision_type=$user->vision_type;
    $this->language_id = $user->language_id;
    $this->original_language_id = $user->language_id;
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
            'is_sound_on' => ['boolean'],
            'vision_type'=>['required', 'string', 'max:255'],
            'language_id' => ['required', 'exists:language,language_id'],
            'profile_picture' => ['nullable', 'image', 'max:1024', 'mimes:jpg,jpeg,png'],
        ]);

        // Compare with the value from the database before saving
        $languageChanged = $validated['language_id'] != $user->language_id;

        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }
                
        if ($this->profile_picture) {
            do {
                $filename = uniqid() .'.' . $this->profile_picture->getClientOriginalExtension();
                // Check if file exists in the profile_pictures disk
                $exists = \Storage::disk('profile_pictures')->exists($filename);
            } while ($exists);
            $path = $this->profile_picture->storeAs('', $filename, 'profile_pictures');
            $validated['profile_picture_url'] = $filename;
            $this->profile_picture = null; // Clear file input
        }

        $user->fill($validated);
        $user->save();
        $this->dispatch('profile-updated',
            first_name: $user->first_name,
            last_name: $user->last_name,
            is_sound_on: $user->is_sound_on,
            language_id: $user->language_id,
            vision_type: $user->vision_type,
            profile_picture_url: $user->profile_picture_url
        );
      
        if ($languageChanged) {
            $this->dispatch('reload-page-for-language');
        }
    }

    /**
     * Get all available languages for the dropdown.
     */
    public function getLanguagesProperty()
    {
        return Language::all();
    }

}
