<?php

use App\Livewire\Actions\Logout;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
});

Route::post('logout', Logout::class)
    ->name('logout');
