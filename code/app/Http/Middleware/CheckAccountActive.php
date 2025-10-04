<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if account is active
            if (!$user->active) {
                // Log out the user
                Auth::logout();

                // Store a flash message for the next request
                session()->flash('account_deactivated', true);

                // Redirect to login with error message
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact your mentor or administrator.');
            }
        }

        return $next($request);
    }
}
