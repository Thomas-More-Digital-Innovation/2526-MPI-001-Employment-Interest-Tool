<?php

namespace App\Livewire;

//Import mail class
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class SendFeedbackTest extends Component
{
    public $clientName ='';
    public $questionNumber ='';
    public $test ='';

    public $showModal;
    public $message = '';
    public $type = '';
    public $class = '';

    //This function will send an email to mentor during a test
    public function sendMail()
    {
        try {
            //Use the build in mail class from Livewire
            Mail::raw('Uw klant, '. $this->clientName .' heeft een probleem gemeld bij vraag ' . $this->questionNumber . ' van de ' . $this->test . '.', function($message){
                $message->to("maurits.groen06@gmail.com")
                    ->subject("Probleem gemeld bij vraag");
            });

            $this->message = 'De mail is verzonden naar uw mentor.';
            $this->type = 'Success';

        } catch (\Exception $e) {
            //Catch the error + error message.
            $this->message = 'De mail is niet verzonden.';
            $this->type = 'Fout';
        }
    }

    public function render()
    {
        return view('livewire.send-feedback-test');
    }
}
