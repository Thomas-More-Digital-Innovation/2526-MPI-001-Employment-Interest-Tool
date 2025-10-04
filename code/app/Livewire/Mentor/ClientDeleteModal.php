<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientDeleteModal extends Component
{
    public $clientId = null;
    public $showModal = false;
    
    protected $listeners = [
        'open-delete-modal' => 'openModal'
    ];

    public function openModal($data = null)
    {
        // Handle both direct method calls and event dispatches
        $clientId = is_array($data) ? ($data['clientId'] ?? null) : $data;
        
        if (!$clientId) {
            return;
        }
        
        $client = User::findOrFail($clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only delete your own clients.');
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

    public function deleteClient()
    {
        $client = User::findOrFail($this->clientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only delete your own clients.');
            return;
        }

        // Soft delete or deactivate instead of hard delete
        $client->update(['active' => false]);

        $this->dispatch('client-deactivated');
        $this->closeModal();
    }


    public function render()
    {
        return view('livewire.mentor.client-delete-modal');
    }
}
