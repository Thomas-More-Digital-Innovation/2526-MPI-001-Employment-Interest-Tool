<?php

namespace App\Livewire\Mentor;

use Livewire\Component;
use App\Models\User;
use App\Models\Test;

class AssignTestsToClientModal extends Component
{
    public ?User $client = null;
    public array $selectedTests = [];

    /**
     * Receive the client ID from the modal opening
     */
    public function mount(?int $client = null): void
    {
        if ($client) {
            $this->client = User::findOrFail($client);

            // Preselect already assigned tests
            $this->selectedTests = $this->client->tests()->pluck('test.test_id')->toArray();
        }
    }

    public function assign(): void
    {
        // Sync selected tests for this client
        $this->client->tests()->sync($this->selectedTests);

        $this->dispatch('close-modal', name: 'assign-tests-to-client');
        $this->dispatch('notify', message: __('Tests assigned successfully!'));
    }

    public function render()
    {
        return view('livewire.mentor.assign-tests-to-client-modal', [
            'tests' => Test::all(), // Fetch all available tests
        ]);
    }

    protected $listeners = ['open-assign-tests-modal' => 'openForClient'];

    public function openForClient(int $clientId): void
    {
        $this->client = User::findOrFail($clientId);
        $this->selectedTests = $this->client->tests()->pluck('test.test_id')->toArray();

        // Open the modal
        $this->dispatch('modal-open', name: 'assign-tests-to-client');
    }
}
