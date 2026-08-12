<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\WebExperienceEntryController;
use App\NativeComponents\WebExperience;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::inertia('/login', 'auth/login')->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::inertia('/register', 'auth/register')->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:registration')
        ->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::inertia('/home', 'welcome')->name('home');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/web-experience-entry', WebExperienceEntryController::class)
    ->name('mobile.web.entry');

Route::native('/mobile', WebExperience::class)->name('mobile.home');
