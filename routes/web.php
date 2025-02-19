<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangeRequestController;

Route::get('/', function () {
    return redirect()->route('change-requests.index');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // Change Request routes
     Route::prefix('change-requests')->name('change-requests.')->group(function () {
        Route::get('/', [ChangeRequestController::class, 'index'])->name('index');
        Route::get('/new', [ChangeRequestController::class, 'changeRequest'])->name('new');
        Route::post('/', [ChangeRequestController::class, 'store'])->name('store');
        Route::get('/{changeRequest}', [ChangeRequestController::class, 'show'])->name('show');
        Route::get('/{changeRequest}/edit', [ChangeRequestController::class, 'editDraft'])->name('edit');
        Route::post('/draft', [ChangeRequestController::class, 'storeDraft'])->name('storeDraft');
        Route::post('/{changeRequest}/comments', [ChangeRequestController::class, 'storeComment'])->name('storeComment');
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

require __DIR__.'/auth.php';
