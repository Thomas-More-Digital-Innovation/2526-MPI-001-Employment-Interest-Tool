<?php

namespace App\Livewire;

use App\Models\Test;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ClientTestPicker extends Component
{
    public $tests;
    protected $userId;

    public function startTest(int $testId) {
        session([
            'testId' => $testId,
            'userId' => $this->userId,
        ]);
        return redirect()->route('client.test');
    }

    public function mount() {
        $this->tests = Test::all();
        $this->userId = Auth::id();
    }
    public function render()
    {
        return view('livewire.client-test-picker');
    }
}
