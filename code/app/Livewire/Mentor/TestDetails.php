<?php

namespace App\Livewire\Mentor;

use App\Models\TestAttempt;
use App\Models\User;
use Livewire\Component;

class TestDetails extends Component
{
    public $testAttemptId;
    public $testClientId;
    public $clientInfo;
    public $attempt;
    public $index = 1;
    public function mount()
    {
        // Prefer query params, fallback to flashed/session values for compatibility
        $this->testClientId = session('testUser');
        $this->testAttemptId = session('testAttempt');

        $this->loadUser();
        $this->loadAttempt();

    }

    public function loadUser(): void
    {
        $this->clientInfo = null;


        $this->clientInfo = User::find($this->testClientId);
        
    }

    public function loadAttempt(): void
    {
        $this->attempt = null;

        // Use the model primary key (test_attempt_id) by using find() so Eloquent respects the model's primaryKey
        $this->attempt = TestAttempt::with(['test', 'answers.question', 'user'])
            ->find($this->testAttemptId);

        // If we didn't get clientInfo from the request/session but the attempt includes the user relation, use it.
        if (! $this->clientInfo && $this->attempt && isset($this->attempt->user)) {
            $this->clientInfo = $this->attempt->user;
        }
    }

    public function render()
    {
        return view('livewire.mentor.test-details');
    }
}