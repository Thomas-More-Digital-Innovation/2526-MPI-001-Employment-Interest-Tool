<?php

namespace App\Livewire\Roles\Superadmin;

use App\Models\Test;
use Livewire\Component;

class TestEdit extends Component
{

    public $tests;

    public function mount() {
        $this->tests = Test::all();
    }

    public function loadTest(int $id)
    {
        $test = Test::with('questions')->findOrFail($id);

        // prepare array like TestCreation expects
        $questions = $test->questions->map(function ($q, $i) {
            return [
                'question_number' => $i + 1,
                'title' => $q->question ?? '',
                'description' => $q->image_description ?? '',
                'interest' => $q->interest_field_id ?? -1,
                'circleFill' => 'green',
                'media_link' => $q->media_link ?? null, // Added media_link to the array
            ];
        })->toArray();

        // now redirect to test creation and pass data
        session([
            'edit_test_id' => $test->test_id,
            'edit_test_name' => $test->test_name,
            'edit_questions' => $questions,
        ]);

        return redirect()->route('superadmin.test.create');
    }

    public function render()
    {
        return view('livewire.roles.superadmin.test-edit');
    }
}
