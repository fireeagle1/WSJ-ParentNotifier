<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeContactController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Public sign-up routes (token-based for unique links)
Route::get('/signup/{token?}', [SignUpController::class, 'show'])->name('signup.show');
Route::post('/signup/{token?}', [SignUpController::class, 'store'])->name('signup.store');

/*
|--------------------------------------------------------------------------
| Feed Routes (Token-authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['token.auth'])->group(function () {
    Route::get('/feed/{token}', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/feed/{token}/map', [FeedController::class, 'map'])->name('feed.map');
    Route::get('/feed/{token}/leaders', [FeedController::class, 'leaders'])->name('feed.leaders');
    Route::get('/feed/{token}/contacts', [FeedController::class, 'contacts'])->name('feed.contacts');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Session-authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Dashboard home
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Posts CRUD
    Route::resource('posts', PostController::class)->except(['show']);

    // Parents management
    Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
    Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
    Route::delete('/parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');
    Route::post('/parents/{parent}/send-reset', [ParentController::class, 'sendPasswordReset'])->name('parents.send-reset');
    Route::post('/parents/toggle-signup', [ParentController::class, 'toggleSignup'])->name('parents.toggle-signup');

    // Leaders management
    Route::get('/leaders', [LeaderController::class, 'index'])->name('leaders.index');
    Route::post('/leaders', [LeaderController::class, 'store'])->name('leaders.store');
    Route::post('/leaders/{leader}/toggle-visibility', [LeaderController::class, 'toggleVisibility'])->name('leaders.toggle-visibility');
    Route::post('/leaders/{leader}/send-reset', [LeaderController::class, 'sendPasswordReset'])->name('leaders.send-reset');
    Route::get('/leaders/{leader}/edit-bio', [LeaderController::class, 'editBio'])->name('leaders.edit-bio');
    Route::put('/leaders/{leader}/update-bio', [LeaderController::class, 'updateBio'])->name('leaders.update-bio');
    Route::delete('/leaders/{leader}', [LeaderController::class, 'destroy'])->name('leaders.destroy');

    // Home contacts schedule
    Route::get('/contacts', [HomeContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [HomeContactController::class, 'store'])->name('contacts.store');
    Route::delete('/contacts/{contact}', [HomeContactController::class, 'destroy'])->name('contacts.destroy');

    // Settings
    Route::get('/settings', [LeaderController::class, 'settings'])->name('leaders.settings');
    Route::post('/settings/profile', [LeaderController::class, 'updateProfile'])->name('leaders.update-profile');
    Route::post('/settings/branding', [LeaderController::class, 'updateBranding'])->name('leaders.update-branding');
    Route::delete('/settings/header-image', [LeaderController::class, 'removeHeaderImage'])->name('leaders.remove-header-image');
});

require __DIR__.'/auth.php';
