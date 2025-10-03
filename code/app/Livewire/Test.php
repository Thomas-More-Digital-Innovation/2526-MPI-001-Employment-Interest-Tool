<?php

namespace App\Livewire;

use App\Models\Question;
use Livewire\Component;

class Test extends Component
{

    public $testId = 1;
    public $title;
    public $image;
    public $imageDescription;
    public $audio;

    public $questionNumber = 1;

    public $totalQuestions;

    public $testName;
    public $clientName;

    public $previousEnabled = true;

    public function mount()
    {
        $this->totalQuestions = Question::where('test_id', $this->testId)->count();
        $this->testName = \App\Models\Test::where('test_id', $this->testId)->value('test_name');

    }

    public function render()
    {

        $question = Question::where('test_id', $this->testId)
            ->where('question_number', $this->questionNumber)
            ->firstOrFail(['question', 'media_link', 'image_description', 'sound_link']);

        $this->title = $question->question;
        $this->image = $question->media_link;
        $this->imageDescription = $question->image_description;
        $this->audio = $question->sound_link;

        return view('livewire.test')->layout('components.layouts.test');
    }

    public function close()
    {
       return redirect()->route('dashboard');
    }

    public function like()
    {
        $this->nextQuestion();
    }

    public function dislike()
    {
        $this->nextQuestion();
    }

    public function previous()
    {

        $this->previousQuestion();
    }

    public function next()
    {
        $this->nextQuestion();
    }

    /* DRY */
    private function nextQuestion()
    {
        if ($this->questionNumber == $this->totalQuestions) {
            return;
        }

        $this->questionNumber++;
        $this->previousEnabled = true;

    }

    private function previousQuestion()
    {
        if ($this->questionNumber == 1) {
            return;
        }

        $this->questionNumber--;
        $this->previousEnabled = false;
    }
}
