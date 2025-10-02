<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Faq extends Component
{
    public $faqs;

    /**
     * Create a new component instance.
     */
    public function __construct($faqs = null)
    {
        $this->faqs = $faqs ?? \App\Models\Faq::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.faq');
    }
}
