<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientRestoreModal extends Component
{
    public $clientId;
    public $showModal = false;

    public function openModal($clientId)
    {
        $client = User::findOrFail($clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only restore your own clients.');
            return;
        }

        $this->clientId = $clientId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->clientId = null;
    }

    public function restoreClient()
    {
        $client = User::findOrFail($this->clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only restore your own clients.');
            return;
        }

        $client->update(['active' => true]);

        $this->dispatch('client-restored');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.mentor.client-restore-modal');
    }
}
