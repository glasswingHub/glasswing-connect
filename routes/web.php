<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImporterController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportRecordController;
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

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::post('users', [UserController::class, 'index'])
        ->name('users.index');

    Route::resource('importers', ImporterController::class);
    Route::patch('importers/{importer}/restore', [ImporterController::class, 'restore'])->name('importers.restore');
    Route::delete('importers/{importer}/force-delete', [ImporterController::class, 'forceDelete'])->name('importers.force-delete');
    Route::post('importers/get-source-table-columns', [ImporterController::class, 'getSourceTableColumns'])->name('importers.get-source-table-columns');
    Route::post('importers/get-target-table-columns', [ImporterController::class, 'getTargetTableColumns'])->name('importers.get-target-table-columns');
    Route::get('importers/{importer}/column-mappings', [ImporterController::class, 'getColumnMappings'])->name('importers.get-column-mappings');

    Route::get('imports', [ImportController::class, 'index'])
        ->name('imports.index');
    Route::get('imports/{importer}/records', [ImportRecordController::class, 'records'])
        ->name('import-records.index');
    Route::get('imports/{importer}/records/{record}', [ImportRecordController::class, 'show'])
        ->name('import-records.show');
    

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
