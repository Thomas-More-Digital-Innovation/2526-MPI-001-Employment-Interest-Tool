<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\InterestField;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TestResults extends Component
{
    public $mainInterest;
    public $testAttemptId;
    public function mount()
    {
        $this->testAttemptId = session('testAttemptId');
        if (!$this->testAttemptId) {
            return redirect()->route('dashboard');
        }
        // 1) Load answers with the nested relation
        $answers = Answer::with('question.interestField')
            ->where('test_attempt_id', $this->testAttemptId)
            ->where('answer', true)
            ->get();

        // 2) Group by interest_field_id, pick the largest group
        $topGroup = $answers
            ->groupBy(fn ($a) => $a->question->interest_field_id)
            ->sortByDesc(fn ($g) => $g->count())
            ->first();

        // 3) Extract the InterestField model + count
        if ($topGroup) {
            $field = $topGroup->first()->question->interestField; // Eloquent model
            $this->mainInterest = [
                'interest_field_id'   => $field->interest_field_id,
                'interest_field_name' => $field->name,
                'total'               => $topGroup->count(),
            ];
        } else {
            $this->mainInterest = null;
        }

        // dd($this->mainInterest);
    }

    public $finished = true;
    public function render()
    {
        return view('livewire.test-results');
    }
}
