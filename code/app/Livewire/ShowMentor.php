<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class ShowMentor extends Component
{
    public $mentor;

    public function mount() {
        $user = Auth::user();

        $this->mentor = User::where('user_id', $user->mentor_id)->first();
    }

    public function render()
    {
        return view('livewire.show-mentor');
    }
}
