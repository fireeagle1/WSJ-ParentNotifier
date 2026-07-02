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
    Route::post('/parents/toggle-signup', [ParentController::class, 'toggleSignup'])->name('parents.toggle-signup');

    // Leaders management
    Route::get('/leaders', [LeaderController::class, 'index'])->name('leaders.index');
    Route::post('/leaders', [LeaderController::class, 'store'])->name('leaders.store');
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

/*
|--------------------------------------------------------------------------
| Temporary Debug Route - REMOVE AFTER USE
|--------------------------------------------------------------------------
*/
Route::get('/debug-mail-config', function () {
    // Clear OPcache if available
    $opcacheCleared = function_exists('opcache_reset') ? opcache_reset() : 'opcache not available';

    // Check for any cached config file
    $cachedConfigPath = base_path('bootstrap/cache/config.php');
    $cachedConfigExists = file_exists($cachedConfigPath);

    // Delete it if it exists
    if ($cachedConfigExists) {
        @unlink($cachedConfigPath);
    }

    // Also check if there's a cached config in the app instance
    $app = app();

    // Force reload of env
    $envPath = base_path('.env');
    $envContent = file_get_contents($envPath);
    preg_match('/^MAIL_MAILER=(.*)$/m', $envContent, $matches);
    $rawMailer = isset($matches[1]) ? trim($matches[1]) : 'NOT FOUND';

    // Check if your hosting runs config:cache in a deploy script
    // by looking at common deploy files
    $deployFiles = [];
    foreach (['.cpanel.yml', 'deploy.php', 'Procfile', '.platform.app.yaml', 'nixpacks.toml', 'render.yaml'] as $f) {
        if (file_exists(base_path($f))) {
            $deployFiles[] = $f;
        }
    }

    return response()->json([
        'opcache_cleared' => $opcacheCleared,
        'cached_config_existed' => $cachedConfigExists,
        'cached_config_exists_after_delete' => file_exists($cachedConfigPath),
        'raw_env_mail_mailer' => $rawMailer,
        'config_mail_default_before_clear' => config('mail.default'),
        'env_mail_mailer' => env('MAIL_MAILER'),
        'deploy_files_found' => $deployFiles,
        'bootstrap_cache_dir_contents' => scandir(base_path('bootstrap/cache')),
        'app_config_cached' => $app->configurationIsCached(),
    ]);
});

require __DIR__.'/auth.php';
