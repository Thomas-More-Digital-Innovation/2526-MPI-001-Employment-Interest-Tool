<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Test;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\TestResults;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('home');
})->name('home');

// Override Fortify's login GET route to redirect to home
Route::get('/login', function () {
    // reroute to home page
    return redirect()->route('home');
});


Route::get('/locale/{locale}', function ($locale) {
    $validLocales = \App\Models\Language::pluck('language_code')->toArray();
    if (in_array($locale, $validLocales)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.change');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isMentor()) {
            return redirect()->route('mentor.dashboard');
        } elseif ($user->isResearcher()) {
            return redirect()->route('researcher.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }
    })->name('dashboard');

    Route::get('/test', Test::class)->name('client.test');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/profile/picture/{filename}', function ($filename) {
        $disk = \Illuminate\Support\Facades\Storage::disk('profile_pictures');
        if (!$disk->exists($filename)) {
            abort(404);
        }
        $path = $disk->path($filename);
        return response()->file($path);
    })->name('profile.picture');

    Route::get('/question/image/{filename}', function ($filename) {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (!$disk->exists($filename)) {
            abort(404);
        }
        $path = $disk->path($filename);
        return response()->file($path);
    })->name('question.image');

    // Route to view Test Results
    Route::get('/test-results', TestResults::class)->name('client.test-result');

    // Role-based routes
    Route::middleware(['role:SuperAdmin'])->group(function () {
        // Route::redirect('/', 'dashboard');
        Route::view('superadmin/dashboard', 'roles.superadmin.dashboard')->name('superadmin.dashboard');
        Route::view('superadmin/system', 'roles.superadmin.system')->name('superadmin.system');
    });

    Route::middleware(['role:Admin'])->group(function () {
        // Route::redirect('/', 'dashboard');
        Route::view('admin/dashboard', 'roles.admin.dashboard')->name('admin.dashboard');
        Route::view('admin/example', 'roles.admin.example')->name('admin.example');
    });

    Route::middleware(['role:Mentor'])->group(function () {
        // Route::redirect('/', 'dashboard');
        Route::view('mentor/dashboard', 'roles.mentor.dashboard')->name('mentor.dashboard');
        Route::view('mentor/example', 'roles.mentor.example')->name('mentor.example');
    });

    Route::middleware(['role:Researcher'])->group(function () {
        // Route::redirect('/', 'dashboard');
        Route::view('researcher/dashboard', 'roles.researcher.dashboard')->name('researcher.dashboard');
        Route::view('researcher/example', 'roles.researcher.example')->name('researcher.example');
    });

    Route::middleware(['role:Client'])->group(function () {
        // Route::redirect('/', 'dashboard');
        Route::view('client/dashboard', 'roles.client.dashboard')->name('client.dashboard');
        Route::view('client/example', 'roles.client.example')->name('client.example');
        Route::view('client/taketest', 'roles.client.taketest')->name('client.taketest');
    });

    // Example of multiple roles
    // Route::middleware(['role:SuperAdmin,Admin'])->group(function () {
    //     Route::view('admin/users', 'admin.users')->name('admin.users');
    // });


});

require __DIR__.'/auth.php';
