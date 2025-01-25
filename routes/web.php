<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Maize\MagicLogin\Facades\MagicLink;
use App\Http\Controllers\Auth\MagicLoginController;

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
});

require __DIR__.'/auth.php';


// Add these routes before your other auth routes
Route::middleware('guest')->group(function () {
    Route::get('magic-login', [MagicLoginController::class, 'showMagicLoginForm'])
        ->name('magic-login');

    Route::post('magic-login', [MagicLoginController::class, 'sendMagicLink'])
        ->name('magic-login.email');
});

// Add the magic link verification route
MagicLink::route();