<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use Livewire\Component;

class TestDetails extends Component
{
    public $testId;
    public $tests = [];
    public $testName;
    public function mount()
    {
        $this->testId = "1";
        $this->testName = $this->test->test_name;
        $this->totalQuestions = Question::where('test_id', $this->testId)->count();

    }
    public function viewTests(int $clientId)
    {
        session()->flash('viewingClient', $clientId);
        return redirect()->route('/mentor/clientTests');
    }
    public function render()
    {
        return view('livewire.mentor.test-details');
    }
}