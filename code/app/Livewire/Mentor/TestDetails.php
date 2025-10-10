<?php

namespace App\Livewire\Mentor;

use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use Livewire\Component;

class TestDetails extends Component
{
    public $testAtemptId;
    public $testClientId;
    public function mount()
    {
        $this->viewingClientId = session(key: 'viewingClient');


    }
    public function render()
    {
        return view('livewire.mentor.test-details');
    }
}