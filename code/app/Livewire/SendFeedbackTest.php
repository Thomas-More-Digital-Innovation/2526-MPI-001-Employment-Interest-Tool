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
    public $question ='';
    public $mailMentor='';
    public $website='';

    public $showModal;
    public $message = '';
    public $type = '';
    public $class = '';

    public $onCloseEvent = 'closeModal'; // Default event name

    //This function will send an email to mentor during a test
    public function sendMail()
    {
        try {
            //Use the build in mail class from Livewire
            Mail::send('emails.send-feedback-test', [
                'clientName' => $this->clientName,
                'questionNUmber' => $this->questionNumber,
                'test' => $this->test,
                'website' => $this->website,
                'question' => $this->question
            ], function($message){
                $message->to($this->mailMentor)
                    ->subject("Onduidelijke vraag gemeld");
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

    public function closeModal() {
        $this->dispatch($this->onCloseEvent);

//        return $this->redirect('goole');
    }
}
