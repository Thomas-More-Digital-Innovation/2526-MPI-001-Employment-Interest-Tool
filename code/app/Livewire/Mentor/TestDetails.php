<?php

namespace App\Livewire\Mentor;

use App\Models\InterestField;
use App\Models\Question;
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
    public $currentLocale;
    public $graphData;
    public $graphLabels;
    public $openRow = null; // Track which row is open
    
    public function toggleRow($row)
    {
        $this->openRow = ($this->openRow === $row) ? null : $row;
    }


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
        $this->currentLocale = app()->getLocale();
    }

    public function loadAttempt(): void
    {
        $this->attempt = null;

        // Use the model primary key (test_attempt_id) by using find() so Eloquent respects the model's primaryKey
        $this->attempt = TestAttempt::with(['test', 'answers.question.interestField', 'user'])
            ->find($this->testAttemptId);

        // Build graph labels and data from answers grouped by interest field
        $this->graphLabels = [];
        $this->graphData = [];

        if ($this->attempt && isset($this->attempt->answers)) {
            $locale = $this->currentLocale ?? app()->getLocale();

            // Map of interest_field_id => count of yes answers
            $fieldYesCount = [];

            foreach ($this->attempt->answers as $answer) {
                $question = $answer->question ?? null;
                $interest = $question && isset($question->interestField) ? $question->interestField : null;
                if (! $interest) {
                    continue;
                }

                $fieldId = $interest->interest_field_id;

                // Initialize if not yet present
                if (! array_key_exists($fieldId, $fieldYesCount)) {
                    $fieldYesCount[$fieldId] = 0;
                }

                // Increment count for each yes answer
                if ($answer->answer) {
                    $fieldYesCount[$fieldId]++;
                }
            }

            // Sort by count descending
            arsort($fieldYesCount);

            // Convert map to ordered labels and data
            foreach ($fieldYesCount as $fieldId => $yesCount) {
                // find the InterestField instance from one of the answers to get the name
                $interest = null;
                foreach ($this->attempt->answers as $answer) {
                    $q = $answer->question ?? null;
                    if ($q && isset($q->interestField) && $q->interestField->interest_field_id == $fieldId) {
                        $interest = $q->interestField;
                        break;
                    }
                }

                $label = $interest ? $interest->getName($locale) : ('Field ' . $fieldId);
                $this->graphLabels[] = $label;
                $this->graphData[] = $yesCount;
            }
        }
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