<?php

namespace App\Livewire\Roles\Superadmin;

use Livewire\Component;
use App\Models\InterestField;
use Illuminate\Support\Str;

class TestCreation extends Component
{
    public $interestFields;
    public string $test_name = '';
    public int $selectedQuestion = 0;

    /** @var array<int,array{uid:string,title:string,description:string,interest:int|string|null}> */
    public array $questions = [];

    public function mount(): void
    {
        $this->interestFields = InterestField::all();
        $this->questions[] = $this->blankQuestion();
    }

    protected function blankQuestion(): array
    {
        return [
            'uid' => (string) Str::uuid(),
            'title' => '',
            'description' => '',
            'interest' => null,
        ];
    }

    public function createQuestion(): void
    {
        $this->questions[] = $this->blankQuestion();
        $this->selectedQuestion = count($this->questions) - 1;
    }

    public function selectQuestion(int $index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->selectedQuestion = $index;
        }
    }

    public function removeQuestion(int $index): void
    {
        if (!isset($this->questions[$index])) return;

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);

        if ($this->selectedQuestion >= count($this->questions)) {
            $this->selectedQuestion = max(0, count($this->questions) - 1);
        }
        if (count($this->questions) === 0) {
            $this->questions[] = $this->blankQuestion();
            $this->selectedQuestion = 0;
        }
    }

    public function moveQuestionUp(int $index): void
    {
        if ($index <= 0 || $index >= count($this->questions)) return;

        [$this->questions[$index - 1], $this->questions[$index]] = [
            $this->questions[$index],
            $this->questions[$index - 1],
        ];

        if ($this->selectedQuestion === $index) $this->selectedQuestion = $index - 1;
        elseif ($this->selectedQuestion === $index - 1) $this->selectedQuestion = $index;

        $this->questions = array_values($this->questions);
    }

    public function moveQuestionDown(int $index): void
    {
        if ($index < 0 || $index >= count($this->questions) - 1) return;

        [$this->questions[$index + 1], $this->questions[$index]] = [
            $this->questions[$index],
            $this->questions[$index + 1],
        ];

        if ($this->selectedQuestion === $index) $this->selectedQuestion = $index + 1;
        elseif ($this->selectedQuestion === $index + 1) $this->selectedQuestion = $index;

        $this->questions = array_values($this->questions);
    }

    public function render()
    {
        return view('livewire.roles.superadmin.test-creation');
    }
}
