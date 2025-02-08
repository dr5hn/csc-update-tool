<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangeRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/change-requests/new', [ChangeRequestController::class, 'changeRequest'])->name('change-requests.new');
    Route::post('/change-requests/new', [ChangeRequestController::class, 'changeRequestSave'])->name('change-requests.save');
    Route::get('/states', [ChangeRequestController::class, 'getStates'])->name('states');
    Route::get('/states-dropdown', [ChangeRequestController::class, 'getStatesDropdown'])->name('states-dropdown');
    Route::get('/cities-by-country', [ChangeRequestController::class, 'getCitiesByCountry'])->name('cities-by-country');
    Route::get('/cities-by-state', [ChangeRequestController::class, 'getCitiesByState'])->name('cities-by-state');
    Route::get('/countries', [ChangeRequestController::class, 'getCountries'])->name('countries');
    Route::get('/subregions', [ChangeRequestController::class, 'getSubregions'])->name('subregions');
    Route::get('/regions', [ChangeRequestController::class, 'getRegions'])->name('regions');
    Route::post('/change-requests/draft', [ChangeRequestController::class, 'saveDraft'])->name('change-requests.draft');
    Route::get('/change-requests/draft/{id}', [ChangeRequestController::class, 'getDraft'])->name('change-requests.getDraft');
});

require __DIR__.'/auth.php';
