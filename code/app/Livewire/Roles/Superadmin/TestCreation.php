<?php

namespace App\Livewire\Roles\Superadmin;

use App\Models\Test;
use Livewire\Component;
use App\Models\Question;
use App\Models\InterestField;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestCreation extends Component
{
    use WithFileUploads; // Added the WithFileUploads trait to handle file uploads

    // Creating a placeholder variable to pass interest fields to the test-creation page
    public $interestFields;
    // For restoring the test on the edit part
    public $test_id;
    // Where the name for the whole test will be stored, initialized as empty.
    public string $test_name = '';
    // Variable for the selected question, defaulted to 0 to avoid problems
    public int $selectedQuestion = 0;
    // Search term for filtering interest fields in modal
    public string $interestFieldSearch = '';

    // Array where the questions are stored temporarily
    /** @var array<int,array{question_number:int,title:string,description:string,interest:int|string|null,circleFill:string}> */
    public array $questions = [];

    // Runs on page start, sends needed information (interestfields from db + an array for the questions and an initial blank question)
    public function mount(): void
    {
        // Fetch all interest fields from the database
        $this->interestFields = InterestField::all();
        // retrieve from the session (possible) given data by
        $editId = session()->pull('edit_test_id');
        $editName = session()->pull('edit_test_name');
        $editQuestions = session()->pull('edit_questions');
        // If editing, load the test data into the component's state
        if ($editId !== null) {
            $this->test_id = $editId;
            $this->test_name = $editName ?? '';
            $this->questions = is_array($editQuestions) ? array_values($editQuestions) : [];
        } else {
            $this->questions[] = $this->blankQuestion();
        }
    }

    public function clearSession(): void
    {
        session()->forget(['edit_test_id', 'edit_test_name', 'edit_questions']);
    }

    // This function updates the color of the status circle on the questions
    public function updateCircleFill(int $index): void
    {
        if (!array_key_exists($index, $this->questions) || !is_array($this->questions[$index])) {
            return; // question got deleted or index shifted
        }

        $q =& $this->questions[$index]; // by reference

        $title    = isset($q['title']) ? trim((string) $q['title']) : '';
        $desc     = isset($q['description']) ? trim((string) $q['description']) : '';
        $interest = isset($q['interest']) ? (int) $q['interest'] : -1;

        if ($title !== '' && $interest >= 0) {
            $q['circleFill'] = 'green';
        } elseif ($title !== '' || $desc !== '' || $interest >= 0) {
            $q['circleFill'] = 'yellow';
        } else {
            $q['circleFill'] = 'red';
        }
    }


    public function uploadTest() {
        // Validate and sanitize
        $this->validate([
        'test_name' => 'required|string|min:3',
        'questions' => 'required|array|min:1',
        ]);

        // then check the colors, all must be green to submit test
        foreach ($this->questions as $i => $q) {
            if (($q['circleFill'] ?? null) !== 'green') {
                $this->addError('questions.'.$i, 'Question '.($i + 1).' is incomplete.');
            }
        }

        // if any red/yellow left, don’t continue
        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }
        // check if test is being edited or created
        if ($this->test_id) {
            // if edited then find the test
            $test = Test::findOrFail($this->test_id);
            // update the test name
            $test->update(['test_name' => $this->test_name]);
            // remove all questions for said test
            Question::where('test_id', $test->test_id)->delete();
        } else {
            // otherwise safe to create
            $test = Test::create([
                'test_name' => $this->test_name,
                'active' => 1,
            ]);
        }
        foreach ($this->questions as $index => $question) {
            // create new questions for the test
            Question::create([
                'test_id' => $test->test_id,
                'interest_field_id' => $question['interest'],
                'question_number' => $question['question_number'],
                'question' => $question['title'],
                'image_description' => $question['description'],
                'media_link' => $question['media_link'] ?? null,
                'sound_link' => $question['sound_link'] ?? null,
            ]);
        }
        $this->clearSession();
        return redirect()->route('superadmin.test.manager')->with('success', 'Test saved successfully.');

    }

    // Runs on every update made, used to recalculate the status
    public function updated(string $name, $value)
    {
        // do not run if inputting test_name as that is test global, not question specific
        if ($name === "test_name" || $name == "selectedQuestion") {
            return;
        }

        // Check if an uploaded_image was updated
        if (str_contains($name, '.uploaded_image')) {
            // Extract the question index from the property name
            $exploded_string = explode(".", $name);
            $index = (int) $exploded_string[1];

            // Automatically upload the image
            if (isset($this->questions[$index]['uploaded_image'])) {
                $this->uploadImage($index);
            }
            return;
        }

        if (str_contains($name, '.uploaded_sound')) {
            $parts = explode('.', $name);
            $index = (int) $parts[1];
            if (isset($this->questions[$index]['uploaded_sound'])) {
                $this->uploadSound($index);
            }
            return;
        }

        // We are using this to split the string into an array at every point (question.0.title becomes ["question", "0", "title])
        // We can now use this to take the index
        if (preg_match('/^questions\.(\d+)\./', $name, $m)) {
            $i = (int) $m[1];
            if (array_key_exists($i, $this->questions)) {
                $this->updateCircleFill($i);
            }
        }
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
            'media_link' => null,
            'uploaded_sound' => null,
            'sound_link' => null,
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

        // Clean up audio file if it exists for the deleted question
        $filename = $this->questions[$index]['sound_link'] ?? null;
        if ($filename && Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }

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
        // Track which question is currently selected
        $wasSelectedItem = $this->selectedQuestion === $oldIndex;

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

        // Update selectedQuestion to follow the moved item
        if ($wasSelectedItem) {
            // If the dragged item was selected, follow it to its new position
            $this->selectedQuestion = $newIndex;
        } else if ($this->selectedQuestion > $oldIndex && $this->selectedQuestion <= $newIndex) {
            // If selected item was shifted down (item moved from above to below it)
            $this->selectedQuestion--;
        } else if ($this->selectedQuestion < $oldIndex && $this->selectedQuestion >= $newIndex) {
            // If selected item was shifted up (item moved from below to above it)
            $this->selectedQuestion++;
        }
    }

    public function clearSound(int $index): void
    {
        if (!isset($this->questions[$index])) return;

        // optionally delete file from disk if it exists
        $filename = $this->questions[$index]['sound_link'] ?? null;
        if ($filename && Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }

        // reset question audio state
        $this->questions[$index]['sound_link'] = null;
        $this->questions[$index]['has_audio']  = false;

        // tell the front-end
        $this->dispatch('sound-cleared', index: $index);
    }



    public function uploadSound(int $index): void
    {
        // Check if file was uploaded
        if (!isset($this->questions[$index]['uploaded_sound'])) {
            $this->addError("questions.$index.uploaded_sound", 'No file uploaded.');
            return;
        }
        // Define the uploaded file and it's type
        /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $uploadedFile */
        $uploadedFile = $this->questions[$index]['uploaded_sound'];

        try {
            // Validate the uploaded file
            $this->validate([
                "questions.$index.uploaded_sound" => "required|file|mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/ogg,audio/webm,video/webm,audio/mp4,audio/x-m4a,audio/aac|max:5120",
            ]);
            // Check if file is valid
            if (!$uploadedFile->isValid()) {
                throw new \RuntimeException('Invalid upload.');
            }
            // get file extension, default to webm if none
            $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: 'webm');

            // unique filename in public disk root
            do {
                // q0_ for question 0, q1_ for question 1, etc
                $filename = uniqid('q'.$index.'_').'.'.$extension;
                $exists = Storage::disk('public')->exists($filename);
            } while ($exists);

            // Store the file in the public disk root
            $path = $uploadedFile->storeAs('', $filename, 'public');
            if (!$path) {
                throw new \RuntimeException('Failed storing file.');
            }

            // Save only filename in question state
            $this->questions[$index]['sound_link'] = $filename;
            $this->questions[$index]['has_audio']  = true;

            // notify front-end with absolute playback URL
            $url = route('question.sound', ['filename' => $filename]);
            $this->dispatch('sound-updated', index: $index, url: $url);

            // clear temp
            unset($this->questions[$index]['uploaded_sound']);

        } catch (\Throwable $e) {
            unset($this->questions[$index]['uploaded_sound']);
            throw \Illuminate\Validation\ValidationException::withMessages([
                "questions.$index.uploaded_sound" => 'Failed to upload the sound.',
            ]);
        }
    }


    public function uploadImage(int $index)
    {
        if (!isset($this->questions[$index]['uploaded_image'])) {
            $this->addError('questions.'.$index.'.uploaded_image', 'No file uploaded.');
            return;
        }

        $uploadedFile = $this->questions[$index]['uploaded_image'];

        try {
            // Validate the uploaded file
            $this->validate([
                // TODO: PUT IN .ENV
                'questions.'.$index.'.uploaded_image' => 'image|max:150', // Max 150KB
            ]);

            // Check if file is valid
            if ($uploadedFile->isValid()) {
                $extension = strtolower($uploadedFile->getClientOriginalExtension());
                do {
                    $filename = uniqid().'.'.$extension;
                    $exists = Storage::disk('public')->exists($filename);
                } while ($exists);

                // Store the file in the public disk root
                $path = $uploadedFile->storeAs('', $filename, 'public');

                if ($path) {
                    // Update the media_link for the question with only the filename
                    $this->questions[$index]['media_link'] = $filename;
                }
            }

            // Clear the uploaded image after processing
            unset($this->questions[$index]['uploaded_image']);
        } catch (\Exception $e) {
            // Clear the file input first
            unset($this->questions[$index]['uploaded_image']);

            // Then throw the exception with custom message
            throw \Illuminate\Validation\ValidationException::withMessages([
                'questions.'.$index.'.uploaded_image' => 'Failed to upload the image.',
            ]);
        }
    }

    // Get filtered interest fields based on search term
    public function getFilteredInterestFieldsProperty()
    {
        if (empty($this->interestFieldSearch)) {
            return $this->interestFields;
        }

        $search = strtolower($this->interestFieldSearch);
        $locale = app()->getLocale();

        return $this->interestFields->filter(function ($field) use ($search, $locale) {
            $name = strtolower($field->getName($locale));
            return str_contains($name, $search);
        });
    }

    public function render()
    {
        // Pass the sound URL of the selected question to the view
        $idx = $this->selectedQuestion;
        $soundUrl = null;
        if (isset($this->questions[$idx]['sound_link']) && $this->questions[$idx]['sound_link']) {
            $soundUrl = route('question.sound', ['filename' => $this->questions[$idx]['sound_link']]);
        }
        return view('livewire.roles.superadmin.test-creation', compact('soundUrl'));
    }
}
