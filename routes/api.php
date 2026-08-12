<?php

use App\Actions\Auth\IssueDeviceToken;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\CurrentUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::middleware('throttle:api-authentication')->group(function (): void {
        Route::post('/auth/login', [AuthenticatedSessionController::class, 'store'])
            ->name('auth.login');
        Route::post('/auth/register', [RegisteredUserController::class, 'store'])
            ->name('auth.register');
    });

    Route::middleware(['auth:sanctum', 'abilities:'.IssueDeviceToken::ABILITY])->group(function (): void {
        Route::get('/user', CurrentUserController::class)->name('user.show');
        Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('auth.logout');
    });
});
