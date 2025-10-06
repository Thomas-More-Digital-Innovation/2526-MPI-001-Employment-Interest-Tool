<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Appearance extends Component
{
    /**
     * Render the component.
     */
    public function render()
    {
        $user = Auth::user();

        if ($user->isClient()) {
            return view('livewire.settings.appearance')->layout('components.layouts.app.headerAIT');
        }

        return view('livewire.settings.appearance')->layout('components.layouts.app');
    }
}
