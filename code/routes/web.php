<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Test;
use App\Models\Faq;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Livewire\TestResults;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
})->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/test', Test::class)->name('client.test');

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');


    // Route to view Test Results
    Route::get('/test-results', TestResults::class)->name('client.test-result');


    // Role-based routes
    Route::middleware(['role:SuperAdmin'])->group(function () {
        Route::view('superadmin/system', 'roles.superadmin.system')->name('superadmin.system');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::view('admin/example', 'roles.admin.example')->name('admin.example');
    });

    Route::middleware(['role:Mentor'])->group(function () {
        Route::view('mentor/example', 'roles.mentor.example')->name('mentor.example');
    });

    Route::middleware(['role:Client'])->group(function () {
        Route::view('client/example', 'roles.client.example')->name('client.example');
    });

    // Example of multiple roles
    // Route::middleware(['role:SuperAdmin,Admin'])->group(function () {
    //     Route::view('admin/users', 'admin.users')->name('admin.users');
    // });


});

require __DIR__.'/auth.php';
