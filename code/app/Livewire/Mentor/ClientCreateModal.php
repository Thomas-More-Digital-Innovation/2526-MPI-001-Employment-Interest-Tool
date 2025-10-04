<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use App\Models\Role;
use App\Models\Organisation;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientCreateModal extends Component
{
    // Client form properties
    public $first_name = '';
    public $last_name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $organisation_id = '';
    public $language_id = '';
    public $vision_type = 'normal';
    public $is_sound_on = true;
    public $active = true;

    public $showModal = false;

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'organisation_id' => ['required', 'exists:organisation,organisation_id'],
            'language_id' => ['required', 'exists:language,language_id'],
            'vision_type' => ['required', 'string', 'max:255'],
            'is_sound_on' => ['boolean'],
            'active' => ['boolean'],
        ];
    }

    public function mount()
    {
        $mentor = Auth::user();
        $this->organisation_id = $mentor->organisation_id ?? '';
        $this->language_id = $mentor->language_id ?? '';
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function createClient()
    {
        $this->validate();

        $mentor = Auth::user();

        // Create the client
        $client = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'organisation_id' => $this->organisation_id,
            'language_id' => $this->language_id,
            'vision_type' => $this->vision_type,
            'is_sound_on' => $this->is_sound_on,
            'active' => $this->active,
            'mentor_id' => $mentor->user_id,
            'first_login' => true,
        ]);

        // Assign Client role
        $clientRole = Role::where('role', Role::CLIENT)->first();
        if ($clientRole) {
            $client->roles()->attach($clientRole->role_id);
        }

        $this->dispatch('client-created');
        $this->closeModal();
    }

    public function resetForm()
    {
        $mentor = Auth::user();

        $this->first_name = '';
        $this->last_name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->organisation_id = $mentor->organisation_id ?? '';
        $this->language_id = $mentor->language_id ?? '';
        $this->vision_type = 'normal';
        $this->is_sound_on = true;
        $this->active = true;

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.mentor.client-create-modal', [
            'organisations' => Organisation::all(),
            'languages' => Language::all(),
        ]);
    }
}
