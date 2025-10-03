<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return view('roles.superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return view('roles.admin.dashboard');
        } elseif ($user->isMentor()) {
            return view('roles.mentor.dashboard');
        } elseif ($user->isClient()) {
            return view('roles.client.dashboard');
        } else {
            return view('welcome');
        }
        
        // Fallback for users without roles
        // NONE
    }
}