<?php

namespace App\Livewire\Mentor;

use Livewire\Component;
use App\Models\User;
use App\Models\TestAttempt;
class ClientTests extends Component
{
    public $viewingClientId;
    public $viewingClient;
    public $testAttempts = [];
    public function mount()

    {
        $this->viewingClientId = session('viewingClient');
        $this->loadViewingClient();
    }

    protected function loadViewingClient(): void
    {
        $this->viewingClient = null;

        if (!$this->viewingClientId) {
            return;
        }

        $this->viewingClient = User::find($this->viewingClientId);
        $this->testAttempts = TestAttempt::query()
            ->join('test','testAttempt.test_id', '=', 'test.test_id')
            ->where('user_id', $this->viewingClientId)
            ->where('test_id', '!=', null)
            ->select('testAttempt.*')
            ->select('test.*')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Returns a collection of all attempts (no pagination).
     * Tries common relations on the User model: testAttempts, attempts.
     * Falls back to App\Models\TestAttempt with user_id if present.
     */
    public function getAttemptsProperty()
    {
        if (!$this->viewingClient) {
            return collect();
        }

        if (method_exists($this->viewingClient, 'testAttempts')) {
            return $this->viewingClient->testAttempts()->orderBy('created_at', 'desc')->get();
        }

        if (method_exists($this->viewingClient, 'attempts')) {
            return $this->viewingClient->attempts()->orderBy('created_at', 'desc')->get();
        }



        return collect();
    }

    public function render()
    {
        return view('livewire.mentor.client-tests', [
            'attempts' => $this->testAttempts
        ]);
    }
}
