<?php

use App\Http\Controllers\Auth\MagicLinkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GitHubController;

Route::middleware('guest')->group(function () { 
    Route::get('auth/github', [GitHubController::class, 'redirect'])->name('github.login');
    Route::get('auth/github/callback', [GitHubController::class, 'callback']);
    Route::get('login', [MagicLinkController::class, 'create'])->name('login');
    Route::post('login', [MagicLinkController::class, 'store']);
    Route::get('/magic-link/{token}', [MagicLinkController::class, 'verify'])
        ->middleware('signed')
        ->name('magic-link.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [MagicLinkController::class, 'destroy'])->name('logout');
});