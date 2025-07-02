<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('registrations', App\Http\Controllers\RegistrationController::class)
        ->only(['index', 'show'])
        ->names('registrations');
    Route::post('registrations', [App\Http\Controllers\RegistrationController::class, 'index'])
        ->name('registrations.index');
    Route::get('registrations/{registration}/import', [App\Http\Controllers\RegistrationController::class, 'import'])
        ->name('registrations.import');
    Route::post('registrations/{registration}/process_import', [App\Http\Controllers\RegistrationController::class, 'process_import'])
        ->name('registrations.process_import');
    Route::post('registrations/set_group', [App\Http\Controllers\RegistrationController::class, 'setGroup'])
        ->name('registrations.set_group');
});

require __DIR__.'/auth.php';
