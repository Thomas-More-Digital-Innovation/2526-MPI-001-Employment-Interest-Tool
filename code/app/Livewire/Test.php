<?php

namespace App\Livewire;

use Livewire\Component;

class Test extends Component
{

    public $title = "Office Work";
    public $image = "/assets/office-work.png";
    public $audio = "https://jackmcdade.com/audio/ouch7.mp3";

    public $questionNumber = 1;

    public $totalQuestions = 2;

    public $testName = 'Arbeidsinteressetest';
    public $clientName = 'Jef Los';


    public function render()
    {
        return view('livewire.test')->layout('components.layouts.test');
    }

    public function close()
    {
        $this->title = "close";
    }

    public function like()
    {
        $this->title = "liked";
    }

    public function dislike()
    {
        $this->title = "disliked";
    }

    public function previous()
    {
        $this->title = "previous";
    }

    public function next()
    {
        $this->title = "next";
    }
}
