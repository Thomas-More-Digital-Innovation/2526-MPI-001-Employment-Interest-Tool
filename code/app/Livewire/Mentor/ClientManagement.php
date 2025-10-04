<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientManagement extends Component
{
    public $refreshKey = 0;
    public $deleteModalClientId = null;
    public $showDeleteModal = false;

    protected $listeners = [
        'client-created' => 'handleClientCreated',
        'client-updated' => 'handleClientUpdated',
        'client-deactivated' => 'handleClientDeactivated',
        'client-restored' => 'handleClientRestored',
        'confirm-delete' => 'confirmDelete',
    ];
    
    public function refreshClients()
    {
        $this->dispatch('$refresh');
    }

    public function confirmDelete($clientId)
    {
        $this->deleteModalClientId = $clientId;
        $this->showDeleteModal = true;
    }

    public function deleteClient()
    {
        $client = User::findOrFail($this->deleteModalClientId);

        // Verify this client belongs to the current mentor
        if ($client->mentor_id !== Auth::id()) {
            $this->dispatch('error', 'You can only delete your own clients.');
            return;
        }

        $client->update(['active' => false]);
        $this->showDeleteModal = false;
        $this->deleteModalClientId = null;
        $this->refreshKey++;
        $this->dispatch('success', 'Client deactivated successfully.');
    }

    public function handleClientCreated()
    {
        $this->refreshKey++;
        $this->dispatch('$refresh');
        $this->dispatch('success', 'Client created successfully.');
    }

    public function handleClientUpdated()
    {
        $this->refreshKey++;
        $this->dispatch('$refresh');
        $this->dispatch('success', 'Client updated successfully.');
    }

    public function handleClientDeactivated()
    {
        $this->refreshKey++;
        $this->dispatch('$refresh');
        $this->dispatch('success', 'Client deactivated successfully.');
    }

    public function handleClientRestored()
    {
        $this->refreshKey++;
        $this->dispatch('$refresh');
        $this->dispatch('success', 'Client restored successfully.');
    }

    public function render()
    {
        return view('livewire.mentor.client-management');
    }
}
