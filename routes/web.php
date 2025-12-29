<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\FortnoxController;
use App\Http\Controllers\FortnoxWebhookController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// Legal pages
Route::get('/legal/user-agreement', function () {
    $markdown = file_get_contents(base_path('docs/legal/user-agreement.md'));
    return view('legal.document', [
        'title' => 'Användaravtal',
        'content' => \Illuminate\Support\Str::markdown($markdown),
    ]);
})->name('legal.user-agreement');

Route::get('/', function () {
    // Get actual team count and add 5% for display
    $actualCount = \App\Models\Team::count();
    $displayCount = (int) ceil($actualCount * 1.05);

    return view('landing', [
        'companyCount' => max($displayCount, 1), // Show at least 1
    ]);
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Encryption routes (before encryption.unlocked middleware)
    Route::get('/encryption/setup', [EncryptionController::class, 'showSetup'])
        ->name('encryption.setup');
    Route::post('/encryption/setup', [EncryptionController::class, 'setup'])
        ->name('encryption.setup.store');
    Route::post('/encryption/download-recovery', [EncryptionController::class, 'downloadRecovery'])
        ->name('encryption.download-recovery');
    Route::get('/encryption/download-recovery-verified', [EncryptionController::class, 'downloadRecoveryVerified'])
        ->name('encryption.download-recovery-verified');
    Route::get('/encryption/unlock', [EncryptionController::class, 'showUnlock'])
        ->name('encryption.unlock');
    Route::post('/encryption/unlock', [EncryptionController::class, 'unlock'])
        ->name('encryption.unlock.store');
    Route::post('/encryption/lock', [EncryptionController::class, 'lock'])
        ->name('encryption.lock');
    Route::get('/encryption/status', [EncryptionController::class, 'status'])
        ->name('encryption.status');

    // Dashboard - accessible without encryption (shows empty state if no Fortnox)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Fortnox routes - encryption required for connect, handled in controller
    Route::get('/fortnox/connect', [FortnoxController::class, 'connect'])
        ->name('fortnox.connect');
    Route::get('/fortnox/callback', [FortnoxController::class, 'callback'])
        ->name('fortnox.callback');
    Route::post('/fortnox/disconnect', [FortnoxController::class, 'disconnect'])
        ->name('fortnox.disconnect');

    // Routes that require encryption to be unlocked (for managing encrypted data)
    Route::middleware(['encryption.unlocked'])->group(function () {
        // Encryption management routes
        Route::get('/encryption/change-passphrase', [EncryptionController::class, 'showChangePassphrase'])
            ->name('encryption.change-passphrase');
        Route::post('/encryption/change-passphrase', [EncryptionController::class, 'changePassphrase'])
            ->name('encryption.change-passphrase.store');
        Route::post('/encryption/session-token', [EncryptionController::class, 'createSessionToken'])
            ->name('encryption.session-token');
    });

    // Billing routes (don't require encryption)
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::get('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/resume', [BillingController::class, 'resume'])->name('billing.resume');
});

Route::post('/stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

Route::post('/fortnox/webhook', [FortnoxWebhookController::class, 'handle'])
    ->name('fortnox.webhook');
