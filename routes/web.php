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
    Route::get('/states', [ChangeRequestController::class, 'getStates'])->name('states');
    Route::get('/states-dropdown', [ChangeRequestController::class, 'getStatesDropdown'])->name('states-dropdown');
    Route::get('/cities-by-country', [ChangeRequestController::class, 'getCitiesByCountry'])->name('cities-by-country');
    Route::get('/cities-by-state', [ChangeRequestController::class, 'getCitiesByState'])->name('cities-by-state');
});

require __DIR__.'/auth.php';
