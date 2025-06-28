<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangeRequestController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ChangeRequestController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Change Request routes
    Route::prefix('change-requests')->name('change-requests.')->group(function () {
        Route::get('/', [ChangeRequestController::class, 'index'])->name('index');
        Route::get('/new', [ChangeRequestController::class, 'changeRequest'])->name('new');
        Route::post('/', [ChangeRequestController::class, 'store'])
            ->name('store')
            ->middleware(app()->environment('testing') ? [] : ['api.rate_limit:3,1']); // 3 submissions per minute
        Route::get('/{changeRequest}', [ChangeRequestController::class, 'show'])->name('show');
        Route::get('/{changeRequest}/edit', [ChangeRequestController::class, 'editDraft'])->name('edit');
        Route::post('/draft', [ChangeRequestController::class, 'storeDraft'])
            ->name('storeDraft')
            ->middleware(app()->environment('testing') ? [] : ['api.rate_limit:5,1']); // 5 draft saves per minute
        Route::post('/{changeRequest}/comments', [ChangeRequestController::class, 'storeComment'])
            ->name('storeComment')
            ->middleware(app()->environment('testing') ? [] : ['api.rate_limit:10,1']); // 10 comments per minute
        Route::get('/{changeRequest}/sql', [ChangeRequestController::class, 'exportSQL'])
            ->name('sql');
        Route::get('/{changeRequest}/sql/download', [ChangeRequestController::class, 'downloadSQL'])
            ->name('sql.download');
        Route::post('/{changeRequest}/approve', [ChangeRequestController::class, 'approve'])
            ->name('approve')
            ->middleware('admin');
        Route::post('/{changeRequest}/reject', [ChangeRequestController::class, 'reject'])
            ->name('reject')
            ->middleware('admin');
        Route::post('/{changeRequest}/mark-incorporated', [ChangeRequestController::class, 'markIncorporated'])
            ->name('markIncorporated')
            ->middleware('admin');
        Route::post('/{changeRequest}/verify', [ChangeRequestController::class, 'verifyChanges'])
            ->name('verifyChanges')
            ->middleware('admin');
        Route::post('/bulk-mark-incorporated', [ChangeRequestController::class, 'bulkMarkIncorporated'])
            ->name('bulkMarkIncorporated')
            ->middleware('admin');
        Route::post('/bulk-verify', [ChangeRequestController::class, 'bulkVerifyChanges'])
            ->name('bulkVerifyChanges')
            ->middleware('admin');
    });

    // Partial Routes
    Route::get('/states', [ChangeRequestController::class, 'getStates'])->name('states');
    Route::get('/states-dropdown', [ChangeRequestController::class, 'getStatesDropdown'])->name('states-dropdown');
    Route::get('/cities-by-country', [ChangeRequestController::class, 'getCitiesByCountry'])->name('cities-by-country');
    Route::get('/cities-by-state', [ChangeRequestController::class, 'getCitiesByState'])->name('cities-by-state');
    Route::get('/countries', [ChangeRequestController::class, 'getCountries'])->name('countries');
    Route::get('/subregions', [ChangeRequestController::class, 'getSubregions'])->name('subregions');
    Route::get('/regions', [ChangeRequestController::class, 'getRegions'])->name('regions');
});

require __DIR__ . '/auth.php';
