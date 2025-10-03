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
        ]);

        // Compare with the value from the database before saving
        $languageChanged = $validated['language_id'] != $user->language_id;

        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }

        // Handle profile picture upload separately with validation
        if ($this->profile_picture) {
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = strtolower($this->profile_picture->getClientOriginalExtension());
            $sizeKB = $this->profile_picture->getSize() / 1024;
            if (!in_array($extension, $allowedExtensions)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'profile_picture' => 'File must be jpg, jpeg, or png.'
                ]);
            }
            if ($sizeKB > 1024) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'profile_picture' => 'File must be at most 1024 KB.'
                ]);
            }
            do {
                $filename = uniqid() .'.' . $extension;
                $exists = \Storage::disk('profile_pictures')->exists($filename);
            } while ($exists);
            $path = $this->profile_picture->storeAs('', $filename, 'profile_pictures');
            $user->profile_picture_url = $filename;
            $this->profile_picture = null; // Clear file input
        }

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
