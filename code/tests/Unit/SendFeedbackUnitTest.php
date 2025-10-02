<?php

namespace Tests\Unit;

use App\Livewire\SendFeedbackTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SendFeedbackUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_an_email_successfully()
    {
        // Arrange: Mock Mail::raw
        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function ($content, $callback) {
                return str_contains($content, 'Uw klant, Jan heeft een probleem gemeld bij vraag 5 van de RekenTest.');
            });

        // Act: Run Livewire component
        Livewire::test(SendFeedbackTest::class)
            ->set('clientName', 'Jan')
            ->set('questionNumber', '5')
            ->set('test', 'RekenTest')
            ->call('sendMail')
            ->assertSet('message', 'De mail is verzonden naar uw mentor.')
            ->assertSet('type', 'Success');
    }

    public function test_it_handles_mail_failure()
    {
        // Arrange: Force Mail::raw() to throw an exception
        Mail::shouldReceive('raw')
            ->once()
            ->andThrow(new \Exception('Mail server down'));

        // Act: Run Livewire component
        Livewire::test(SendFeedbackTest::class)
            ->set('clientName', 'Piet')
            ->set('questionNumber', '3')
            ->set('test', 'TaalTest')
            ->call('sendMail')
            // Assert: Component shows error message
            ->assertSet('message', 'De mail is niet verzonden.')
            ->assertSet('type', 'Fout');
    }
}
