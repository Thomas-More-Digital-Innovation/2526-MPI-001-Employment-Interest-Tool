<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user instanceof \App\Models\User) {
            return $next($request);
        }

        if (!$user->active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')->with(
                'status',
                __('Your account has been inactivated. Please contact your mentor or administrator if you believe this is a mistake.')
            );
        }

        return $next($request);
    }
}
