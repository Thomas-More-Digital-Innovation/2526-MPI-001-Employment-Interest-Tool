<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Question;
use App\Models\TestAttempt;
use Carbon\Carbon;
use Livewire\Component;

class Test extends Component
{

    public $testId = 1;
    public $userId = 4;
    public $testAttemptId;

    public $startTime;

    public $question;
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

        if (!$this->testAttemptId) {
            $this->testAttemptId = TestAttempt::create([
                'test_id' => $this->testId,
                'user_id' => $this->userId,
            ])->test_attempt_id;
        }

        $this->newQuestion();
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

    private function close()
    {
       return redirect()->route('dashboard');
    }

    private function like()
    {
        $this->answer(true, null);
        $this->nextQuestion();
    }

    private function dislike()
    {
        $this->answer(false, null);

        $this->nextQuestion();
    }

    private function previous()
    {

        $this->previousQuestion();
    }

    private function next()
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
        $this->newQuestion();

    }

    private function previousQuestion()
    {
        if ($this->questionNumber == 1) {
            return;
        }

        $this->questionNumber--;
        $this->previousEnabled = false;
        $this->newQuestion();
    }

    private function unclear()
    {

    }

    private function newQuestion()
    {
        $this->startTime = Carbon::now();
    }

    private function answer($answer, $unclear)
    {
        $now = Carbon::now();

        $question = Question::where('test_id', $this->testId)
            ->where('question_number', $this->questionNumber)
            ->firstOrFail();

        Answer::updateOrCreate(
            [
                'test_attempt_id' => $this->testAttemptId,
                'question_id'     => $question->question_id,
            ],
            [
                'answer' => $answer,
                'unclear' => $unclear,
                'response_time' => $this->startTime->diffInSeconds($now)
            ]
        );
    }

}
