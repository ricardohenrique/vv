<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\PublishArticleController;
use App\Http\Controllers\Admin\UnpublishArticleController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WebExperienceEntryController;
use App\NativeComponents\WebExperience;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index'])->name('home');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::middleware('guest')->group(function (): void {
    Route::inertia('/login', 'auth/login')->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::resource('articles', AdminArticleController::class)->except(['show', 'destroy']);
        Route::post('/articles/{article}/publish', PublishArticleController::class)->name('articles.publish');
        Route::post('/articles/{article}/unpublish', UnpublishArticleController::class)->name('articles.unpublish');
    });
});

Route::get('/web-experience-entry', WebExperienceEntryController::class)
    ->name('mobile.web.entry');

Route::native('/mobile', WebExperience::class)->name('mobile.home');
