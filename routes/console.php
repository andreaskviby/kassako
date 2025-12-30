<?php

use App\Jobs\SyncFortnoxData;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// DISABLED: Daily sync cannot run without user's encryption session
// With zero-knowledge encryption, syncs only happen when user unlocks their data
// Schedule::call(function () {
//     Team::whereHas('fortnoxConnection', function ($q) {
//         $q->where('is_active', true);
//     })->each(function ($team) {
//         SyncFortnoxData::dispatch($team);
//     });
// })->dailyAt('06:00')->name('sync-fortnox-data');

// Clean up expired encryption session tokens every hour
Schedule::command('encryption:cleanup-tokens')
    ->hourly()
    ->name('cleanup-encryption-tokens')
    ->withoutOverlapping();
