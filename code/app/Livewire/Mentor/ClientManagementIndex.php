<?php

namespace App\Livewire\Mentor;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ClientManagementIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $refreshKey;

    protected $queryString = ['search'];

    public function mount($refreshKey = null)
    {
        $this->refreshKey = $refreshKey;
        $this->resetSearch();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // This will force a refresh when the refreshKey changes
        $this->getClients();
        
        return view('livewire.mentor.client-management-index', [
            'clients' => $this->getClients(),
        ]);
    }

    private function getClients()
    {
        $mentorId = Auth::id();

        return User::whereHas('roles', function ($query) {
                $query->where('role', Role::CLIENT);
            })
            ->where('mentor_id', $mentorId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['organisation', 'language'])
            ->paginate(10);
    }

    public function refreshClients()
    {
        // This method can be called by parent component to refresh the list
        $this->render();
    }

    public function confirmDelete($clientId)
    {
        $this->dispatch('confirm-delete', clientId: $clientId);
    }
}
