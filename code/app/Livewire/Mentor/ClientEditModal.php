<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientEditModal extends Component
{
    public $clientId;
    public $showModal = false;

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

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->clientId, 'user_id')],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'organisation_id' => ['required', 'exists:organisation,organisation_id'],
            'language_id' => ['required', 'exists:language,language_id'],
            'vision_type' => ['required', 'string', 'max:255'],
            'is_sound_on' => ['boolean'],
            'active' => ['boolean'],
        ];
    }

    public function openModal($clientId)
    {
        $client = User::findOrFail($clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only edit your own clients.');
            return;
        }

        $this->clientId = $clientId;
        $this->first_name = $client->first_name;
        $this->last_name = $client->last_name;
        $this->username = $client->username;
        $this->email = $client->email;
        $this->organisation_id = $client->organisation_id;
        $this->language_id = $client->language_id;
        $this->vision_type = $client->vision_type;
        $this->is_sound_on = $client->is_sound_on;
        $this->active = $client->active;
        $this->password = ''; // Don't populate password

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function updateClient()
    {
        $this->validate();

        $client = User::findOrFail($this->clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only edit your own clients.');
            return;
        }

        $updateData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'organisation_id' => $this->organisation_id,
            'language_id' => $this->language_id,
            'vision_type' => $this->vision_type,
            'is_sound_on' => $this->is_sound_on,
            'active' => $this->active,
        ];

        // Only update password if provided
        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $client->update($updateData);

        $this->dispatch('client-updated');
        $this->closeModal();
    }

    public function resetForm()
    {
        $this->clientId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->organisation_id = '';
        $this->language_id = '';
        $this->vision_type = 'normal';
        $this->is_sound_on = true;
        $this->active = true;

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.mentor.client-edit-modal', [
            'organisations' => Organisation::all(),
            'languages' => Language::all(),
        ]);
    }
}
