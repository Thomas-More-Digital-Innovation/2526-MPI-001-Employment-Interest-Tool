<?php

namespace App\Livewire\Roles\Superadmin;

use App\Models\Test;
use Livewire\Component;
use App\Models\Question;
use App\Models\InterestField;

class TestCreation extends Component
{
    // Creating a placeholder variable to pass interest fields to the test-creation page
    public $interestFields;
    // For restoring the test on the edit part
    public $test_id;
    // Where the name for the whole test will be stored, initialized as empty.
    public string $test_name = '';
    // Variable for the selected question, defaulted to 0 to avoid problems
    public int $selectedQuestion = 0;

    // Array where the questions are stored temporarily
    /** @var array<int,array{question_number:int,title:string,description:string,interest:int|string|null,circleFill:string}> */
    public array $questions = [];

    // Runs on page start, sends needed information (interestfields from db + an array for the questions and an initial blank question)
    public function mount(): void
    {
        $this->interestFields = InterestField::all();

        if (session()->has('edit_test_id')) {
            $this->test_id = session('edit_test_id');
            $this->test_name = session('edit_test_name');
            $this->questions = session('edit_questions', []);
        } else {
            $this->questions[] = $this->blankQuestion();
        }
    }
    
    public function clearSession(): void
    {
        session()->forget(['edit_test_id', 'edit_test_name', 'edit_questions']);
    }

    // This function updates the color of the status circle on the questions
    public function updateCircleFill(int $index) {
        // Select the specific question using the index
        $question = $this->questions[$index];

        // Validate title
        $hasTitle = trim($question['title'] ?? '') !== '';
        // Validate description
        $hasDescription = trim($question['description'] ?? '') !== '';
        // Validate interest
        $hasInterest = isset($question['interest']) && $question['interest'] >= 0;

        // If all inputs have been filled, make the circle green
        if ($hasTitle && $hasInterest) {
            $question['circleFill'] = "green";
        // if a couple but not all inputs have been filled, make the circle yellow
        } elseif ($hasTitle || $hasDescription || $hasInterest) {
            $question['circleFill'] = "yellow";
        // if no questions are filled make the circle red
        } else {
            $question['circleFill'] = "red";
        }
        $this->questions[$index] = $question;
    }

    public function uploadTest() {
        $this->validate([
        'test_name' => 'required|string|min:3',
        'questions' => 'required|array|min:1',
        ]);

        // then check the colors
        foreach ($this->questions as $i => $q) {
            if (($q['circleFill'] ?? null) !== 'green') {
                $this->addError('questions.'.$i, 'Question '.($i + 1).' is incomplete.');
            }
        }

        // if any red/yellow left, don’t continue
        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        if (session()->has('edit_test_id')) {
            $test = Test::findOrFail(session('edit_test_id'));
            $test->update(['test_name' => $this->test_name]);
            Question::where('test_id', $test->test_id)->delete();
        } else {
            // otherwise safe to create
            $test = Test::create([
                'test_name' => $this->test_name,
            ]);
        }
        foreach ($this->questions as $index => $question) {
            Question::create([
                'test_id' => $test->test_id,
                'interest_field_id' => $question['interest'],
                'question_number' => $question['question_number'],
                'question' => $question['title'],
                'image_description' => $question['description'],
            ]);
        }
        $this->clearSession();
    }

    // Runs on every update made, used to recalculate the status
    public function updated(string $name, $value) 
    {
        // do not run if inputting test_name as that is test global, not question specific
        if ($name === "test_name") {
            return;
        }

        // We are using this to split the string into an array at every point (question.0.title becomes ["question", "0", "title])
        // We can now use this to take the index
        $exploded_string = explode(".", $name);

        // Send the index to the updateCircleFill function
        $this->updateCircleFill((int) $exploded_string[1]);
    }

    // Defining the initial blank question as a function so it does not have to be rewritten
    protected function blankQuestion(): array
    {
        return [
            'question_number' => count($this->questions) + 1,
            'title' => '',
            'description' => '',
            'interest' => -1,
            'circleFill' => 'red',
        ];
    }

    // Creating a new question (adds a blank question to the array and sets it as the selected question)
    public function createQuestion(): void
    {
        $this->questions[] = $this->blankQuestion();
        $this->selectedQuestion = count($this->questions) - 1;
    }

    // Selecting a question, initial validation checks and then sets the variable to the index of the clicked question
    public function selectQuestion(int $index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->selectedQuestion = $index;
        }
    }

    // Removing a question, 
    public function removeQuestion(int $index): void
    {
        // Validation if question exists, if it doesnt then return, better be safe than sorry :)
        if (!isset($this->questions[$index])) return;
        
        // Removing the question from the array
        unset($this->questions[$index]);
        // resolve index issue (removing does not change indexes for the questions)
        $this->questions = array_values($this->questions);
        
        // If the last question is selected and then deleted, select the newest last qeuestion
        if ($this->selectedQuestion >= count($this->questions)) {
            $this->selectedQuestion = max(0, count($this->questions) - 1);
        }
        // If no questions left then add a new blank one, does not allow test to be empty
        if (count($this->questions) === 0) {
            $this->questions[] = $this->blankQuestion();
            $this->selectedQuestion = 0;
        }
    }
    // When dropping an item, reorder the questions in the list
    public function reorderQuestions(int $oldIndex, int $newIndex): void {
        // First we take the array and declare it locally (looks weird if done directly on $this->questions)
        $items = $this->questions;
        // We use this function to "cut off" the item we picked up
        $moved = array_splice($items, $oldIndex, 1)[0];
        // We use this function again to insert the item at the position we desire, the 4th parameter determines what we add
        array_splice($items, $newIndex, 0, [$moved]);

        // Recalculate the questions numbers (Optional)
        foreach($items as $i => &$q) {
            $q['question_number'] = $i + 1;
        }
        unset($q);

        // return the sorted array to the page :)
        $this->questions = array_values($items);
    }

    public function render()
    {
        return view('livewire.roles.superadmin.test-creation');
    }
}
