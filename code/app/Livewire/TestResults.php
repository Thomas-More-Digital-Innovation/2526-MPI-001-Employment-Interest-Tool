<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\InterestField;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TestResults extends Component
{
    public $mainInterest;
    public $secondInterest;
    public $lastInterest;
    public $testAttemptId;
    public function mount()
    {
        $this->testAttemptId = session('testAttemptId');
        if (!$this->testAttemptId) {
            return redirect()->route('dashboard');
        }

        // 1) Selected answers for this attempt
        $answers = Answer::with(['question.interestField.interestFieldTranslations.language'])
            ->where('test_attempt_id', $this->testAttemptId)
            ->where('answer', true)
            ->get();

        // 2) Get test_id from any answer (bail if none)
        $testId = optional($answers->first())->question->test_id;
        if (!$testId) {
            $this->mainInterest   = null;
            $this->secondInterest = null;
            $this->lastInterest   = null;
            return;
        }

        // 3) Interest fields actually present in this test
        $interestIdsInTest = \App\Models\Question::where('test_id', $testId)
            ->whereNotNull('interest_field_id')
            ->pluck('interest_field_id')
            ->unique()
            ->values();

        // 4) Counts from the selected answers
        $selectedCounts = $answers
            ->groupBy(fn ($a) => $a->question->interest_field_id)
            ->map->count(); // [interest_field_id => total]

        // 5) Normalize all interests used in this test (include 0-picked)
        $locale = app()->getLocale();
        $allInterestsInTest = InterestField::with(['interestFieldTranslations.language'])
            ->whereIn('interest_field_id', $interestIdsInTest)
            ->get()
            ->map(function ($field) use ($selectedCounts, $locale) {
                $id = $field->interest_field_id;
                return [
                    'interest_field_id'   => $id,
                    'interest_field_name' => $field->getName($locale),
                    'total'               => (int) ($selectedCounts[$id] ?? 0),
                ];
            })
            ->values();

        // 6) Most-picked (desc)
        $sortedDesc = $allInterestsInTest->sortByDesc('total')->values();

        // main = first
        $main = $sortedDesc->first();
        $this->mainInterest = $main ?? null;

        // second = first item with different id than main
        $this->secondInterest = null;
        if ($main) {
            $this->secondInterest = $sortedDesc->first(function ($item) use ($main) {
                return $item['interest_field_id'] !== $main['interest_field_id'];
            });
        }

        // 7) Least-picked: only if there is a UNIQUE minimum (no ties).
        $totals = $allInterestsInTest->pluck('total');
        $minTotal = $totals->min();

        // candidates with the minimum total
        $leastCandidates = $allInterestsInTest->filter(
            fn ($i) => $i['total'] === $minTotal
        );

        $mainId   = $this->mainInterest['interest_field_id']   ?? null;
        $secondId = $this->secondInterest['interest_field_id'] ?? null;

        if ($leastCandidates->count() === 1) {
            $candidate = $leastCandidates->first();
            // hide if same as main or second
            if ($candidate['interest_field_id'] !== $mainId
                && $candidate['interest_field_id'] !== $secondId) {
                $this->lastInterest = $candidate;
            } else {
                $this->lastInterest = null;
            }
        } else {
            // no unique least (tie or all equal) -> do not display
            $this->lastInterest = null;
        }

        // Optional debug
        // dd($this->mainInterest, $this->secondInterest, $this->lastInterest, $allInterestsInTest);
    }



    public $finished = true;
    public function render()
    {
        return view('livewire.test-results');
    }
}
