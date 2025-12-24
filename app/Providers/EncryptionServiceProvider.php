<?php

namespace App\Providers;

use App\Listeners\HandleSubscriptionCancellation;
use App\Services\Encryption\AesGcmEncryption;
use App\Services\Encryption\DataEncryptorService;
use App\Services\Encryption\KeyDerivationService;
use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

/**
 * Encryption Service Provider
 *
 * Registers all encryption-related services and event listeners.
 */
class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register as singletons to ensure consistent state
        $this->app->singleton(KeyDerivationService::class);
        $this->app->singleton(AesGcmEncryption::class);

        $this->app->singleton(TeamEncryptionService::class, function ($app) {
            return new TeamEncryptionService(
                $app->make(KeyDerivationService::class),
                $app->make(AesGcmEncryption::class)
            );
        });

        $this->app->singleton(DataEncryptorService::class, function ($app) {
            return new DataEncryptorService(
                $app->make(TeamEncryptionService::class),
                $app->make(AesGcmEncryption::class),
                $app->make(KeyDerivationService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register subscription cancellation listener
        Event::listen(
            WebhookReceived::class,
            HandleSubscriptionCancellation::class
        );

        // Register middleware alias
        $this->app['router']->aliasMiddleware(
            'encryption.unlocked',
            \App\Http\Middleware\RequireEncryptionUnlocked::class
        );
    }
}
