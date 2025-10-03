<?php

namespace App\Livewire;

use App\Models\InterestField;
use Livewire\Component;

class TestResults extends Component
{
    public $mainInterest = 'gardening, outdoor work';
    public $otherInterests = 'Computer and industrial work';
    public $finished = true;
    public function mount() {
        $this->mainInterest = InterestField::all();
    }

    public function render()
    {
        return view('livewire.test-results');
    }
}
