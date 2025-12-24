<?php

use App\Jobs\SyncFortnoxData;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Team::whereHas('fortnoxConnection', function ($q) {
        $q->where('is_active', true);
    })->each(function ($team) {
        SyncFortnoxData::dispatch($team);
    });
})->dailyAt('06:00')->name('sync-fortnox-data');

// Clean up expired encryption session tokens every hour
Schedule::command('encryption:cleanup-tokens')
    ->hourly()
    ->name('cleanup-encryption-tokens')
    ->withoutOverlapping();
