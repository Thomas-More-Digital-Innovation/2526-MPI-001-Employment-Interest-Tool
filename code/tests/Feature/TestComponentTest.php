<?php

namespace Tests\Feature;

use App\Livewire\Test as TestComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TestComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mounts_with_correct_total_questions_and_test_name()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        // Create 3 questions with sequential numbers
        for ($i = 1; $i <= 3; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->assertSet('totalQuestions', 3)
            ->assertSet('testName', $test->test_name);
    }

    public function test_renders_question_data_correctly()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        $question = \Database\Factories\QuestionFactory::new()
            ->forTest($test->test_id)
            ->number(1)
            ->create();

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->assertSet('title', $question->question)
            ->assertSet('image', $question->media_link)
            ->assertSet('imageDescription', $question->image_description)
            ->assertSet('audio', $question->sound_link);
    }

    public function test_next_question_increments_question_number()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        for ($i = 1; $i <= 3; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->call('next')
            ->assertSet('questionNumber', 2)
            ->assertSet('previousEnabled', true);
    }

    public function test_previous_question_decrements_question_number()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        for ($i = 1; $i <= 3; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->set('questionNumber', 2)
            ->call('previous')
            ->assertSet('questionNumber', 1)
            ->assertSet('previousEnabled', false);
    }

    public function test_next_question_does_not_exceed_total_questions()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        for ($i = 1; $i <= 2; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->set('questionNumber', 2)
            ->call('next')
            ->assertSet('questionNumber', 2); // should stay the same
    }

    public function test_previous_question_does_not_go_below_one()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        for ($i = 1; $i <= 2; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->call('previous')
            ->assertSet('questionNumber', 1); // should stay at 1
    }

    public function test_like_and_dislike_move_to_next_question()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        for ($i = 1; $i <= 3; $i++) {
            \Database\Factories\QuestionFactory::new()
                ->forTest($test->test_id)
                ->number($i)
                ->create();
        }

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->call('like')
            ->assertSet('questionNumber', 2)
            ->call('dislike')
            ->assertSet('questionNumber', 3);
    }

    public function test_close_redirects_to_dashboard()
    {
        $test = \Database\Factories\TestFactory::new()->create();

        // Create a question so mount() + render() won't fail
        \Database\Factories\QuestionFactory::new()
            ->forTest($test->test_id)
            ->number(1)
            ->create();

        Livewire::test(TestComponent::class, ['testId' => $test->test_id])
            ->call('close')
            ->assertRedirect(route('dashboard'));
    }

}
