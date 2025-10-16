<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Test\Test;
use App\Livewire\Test\TestContentOverview;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Test\TestResults;
use App\Models\User;
use App\Models\Role;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('dashboard');
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
        $user = Auth::user();
        switch (true) {
            case $user->isClient(): // Client can only view the profile picture of their mentor
            $mentor = User::find($user->mentor_id);
            if (
                !$mentor ||
                $filename !== $mentor->getRawProfilePictureName()
            ) {
                abort(403);
            }
            break;
            case $user->isMentor(): // Mentor can only view their own profile picture
            case $user->isResearcher(): // Researcher can only view their own profile picture
            if ($filename !== $user->getRawProfilePictureName()) {
                abort(403);
            }
            break;
            case $user->isAdmin(): // Admins can view their own profile picture and  all of the profile pictures of the mentors in their organisation
            if ($filename === $user->getRawProfilePictureName()) {
                break;
            }
            $mentor = User::where('organisation_id', $user->organisation_id)
                ->where('profile_picture_url', $filename)
                ->whereHas('roles', function ($q) {
                    $q->where('role', Role::MENTOR);
                })
                ->first();
            if (!$mentor || $filename !== $mentor->getRawProfilePictureName()) {
                abort(403);
            }
            break;
            case $user->isSuperAdmin(): // Superadmins can view their own profile picture and can view the profile pictures of all the admins
            if ($filename === $user->getRawProfilePictureName()) {
                break;
            }
            $admin = User::where('organisation_id', $user->organisation_id)
                ->where('profile_picture_url', $filename)
                ->whereHas('roles', function ($q) {
                    $q->where('role', Role::ADMIN);
                })
                ->first();
            if (!$admin || $filename !== $admin->getRawProfilePictureName()) {
                abort(403);
            }
            break;
            default:
            abort(403);
        }

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
        Route::view('superadmin/dashboard', 'roles.superadmin.dashboard')->name('superadmin.dashboard');
        Route::view('superadmin/system', 'roles.superadmin.system')->name('superadmin.system');
        Route::view('superadmin/manage-researchers', 'roles.superadmin.manage-researchers')->name('superadmin.manage-researchers');
        Route::view('superadmin/interest-field-manager', view: 'roles.superadmin.interest-field-manager')->name('superadmin.interest-field-manager');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::view('admin/dashboard', 'roles.admin.dashboard')->name('admin.dashboard');
        Route::view('admin/feedback', 'roles.admin.feedback')->name('admin.feedback');
        Route::view(uri: 'admin/admin-clients-manager', view: 'roles.admin.admin-clients-manager')->name('admin.admin-clients-manager');
        Route::view(uri:'admin/manage-mentors', view:'roles.admin.manage-mentors')->name('admin.manage-mentors');
    });

    Route::middleware(['role:Mentor'])->group(function () {
        Route::view('mentor/dashboard', 'roles.mentor.dashboard')->name('mentor.dashboard');
        Route::view('mentor/example', 'roles.mentor.example')->name('mentor.example');
        Route::view('mentor/clients-manager', 'roles.mentor.clients-manager')->name('mentor.clients-manager');
        Route::view('mentor/client-tests', ('roles.mentor.client-tests'))->name('mentor.client-tests');
        Route::view('mentor/test-details', ('roles.mentor.test-details'))->name('mentor.test-details');
    });

    Route::middleware(['role:Researcher'])->group(function () {
        Route::view('researcher/dashboard', 'roles.researcher.dashboard')->name('researcher.dashboard');
    });

    Route::middleware(['role:Client'])->group(function () {
        Route::view('client/dashboard', 'roles.client.dashboard')->name('client.dashboard');
        //This is kept as reference
//        Route::view('client/taketest', 'roles.client.taketest')->name('client.taketest');
    });

    Route::middleware(['role:Mentor,Admin,SuperAdmin'])->group(function () {
        Route::view('staff/test-picker', view: 'roles.staff.test-picker')->name('staff.test-picker');
        Route::view('staff/test-content-overview', 'roles.staff.test-content-overview')->name('roles.staff.test-content-overview');
    });


    // Example of multiple roles
    // Route::middleware(['role:SuperAdmin,Admin'])->group(function () {
    //     Route::view('admin/users', 'admin.users')->name('admin.users');
    // });


});

require __DIR__.'/auth.php';
