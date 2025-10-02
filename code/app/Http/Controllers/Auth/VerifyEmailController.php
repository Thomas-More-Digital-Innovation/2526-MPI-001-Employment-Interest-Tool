<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Redirect to dashboard (no email logic).
     */
    public function __invoke(): RedirectResponse
    {
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
