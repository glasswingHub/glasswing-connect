<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImporterController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportRecordController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VolunteerImporterController;
use App\Http\Controllers\BeneficiaryImporterController;
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

    Route::resource('importers', ImporterController::class)->only(['index'])->names('importers');

    Route::resource('importers/volunteers', VolunteerImporterController::class)->names('volunteer_importers');
    Route::patch('importers/volunteers/{importer}/restore', [VolunteerImporterController::class, 'restore'])->name('volunteer_importers.restore');
    Route::delete('importers/volunteers/{importer}/force-delete', [VolunteerImporterController::class, 'forceDelete'])->name('volunteer_importers.force-delete');
    Route::post('importers/volunteers/get-source-table-columns', [VolunteerImporterController::class, 'getSourceTableColumns'])->name('volunteer_importers.get-source-table-columns');
    Route::post('importers/volunteers/get-target-table-columns', [VolunteerImporterController::class, 'getTargetTableColumns'])->name('volunteer_importers.get-target-table-columns');
    Route::get('importers/volunteers/{importer}/column-mappings', [VolunteerImporterController::class, 'getColumnMappings'])->name('volunteer_importers.get-column-mappings');

    Route::resource('importers/beneficiaries', BeneficiaryImporterController::class)->names('beneficiary_importers');
    Route::patch('importers/beneficiaries/{importer}/restore', [BeneficiaryImporterController::class, 'restore'])->name('beneficiary_importers.restore');
    Route::delete('importers/beneficiaries/{importer}/force-delete', [BeneficiaryImporterController::class, 'forceDelete'])->name('beneficiary_importers.force-delete');
    Route::post('importers/beneficiaries/get-source-table-columns', [BeneficiaryImporterController::class, 'getSourceTableColumns'])->name('beneficiary_importers.get-source-table-columns');
    Route::post('importers/beneficiaries/get-target-table-columns', [BeneficiaryImporterController::class, 'getTargetTableColumns'])->name('beneficiary_importers.get-target-table-columns');
    Route::get('importers/beneficiaries/{importer}/column-mappings', [BeneficiaryImporterController::class, 'getColumnMappings'])->name('beneficiary_importers.get-column-mappings');

    Route::get('imports', [ImportController::class, 'index'])
        ->name('imports.index');
    Route::get('imports/{importer}/records', [ImportRecordController::class, 'records'])
        ->name('import-records.index')
        ->middleware('validate.importer.access');
    Route::get('imports/{importer}/records/{record}', [ImportRecordController::class, 'show'])
        ->name('import-records.show')
        ->middleware('validate.importer.access');
    Route::post('imports/{importer}/records/{record}/process_import', [ImportRecordController::class, 'process_import'])
        ->name('import-records.process_import')
        ->middleware('validate.importer.access');

    Route::resource('registrations', App\Http\Controllers\RegistrationController::class)
        ->only(['index', 'show'])
        ->names('registrations');
    Route::get('registrations/{registration}/import', [App\Http\Controllers\RegistrationController::class, 'import'])
        ->name('registrations.import');
    Route::post('registrations/{registration}/process_import', [App\Http\Controllers\RegistrationController::class, 'process_import'])
        ->name('registrations.process_import');
    Route::post('registrations/set_group', [App\Http\Controllers\RegistrationController::class, 'setGroup'])
        ->name('registrations.set_group');
});

require __DIR__.'/auth.php';
