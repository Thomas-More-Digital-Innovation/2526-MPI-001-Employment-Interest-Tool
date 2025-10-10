<?php

namespace App\Livewire\Test;

use Livewire\Component;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\Auth;

class TestOverview extends Component
{
    public $attempts;

    public function mount() {
        $this->attempts = TestAttempt::with('test:test_id,test_name')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.test.test-overview');
    }
}
