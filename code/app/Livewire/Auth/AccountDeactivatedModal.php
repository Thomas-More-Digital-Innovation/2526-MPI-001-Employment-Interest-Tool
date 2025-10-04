<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountDeactivatedModal extends Component
{
    public $showModal = false;

    public function mount()
    {
        // Show modal if account is deactivated during login attempt
        if (session()->has('account_deactivated')) {
            $this->showModal = true;
            session()->forget('account_deactivated');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.auth.account-deactivated-modal');
    }
}
