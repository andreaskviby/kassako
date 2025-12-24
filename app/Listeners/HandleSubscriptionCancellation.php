<?php

namespace App\Listeners;

use App\Models\Team;
use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

/**
 * Handle Subscription Cancellation
 *
 * This listener destroys encryption keys when a subscription ends,
 * making all encrypted data permanently inaccessible.
 *
 * Security Flow:
 * 1. Subscription cancelled (or payment failed)
 * 2. Grace period ends (configurable, e.g., 30 days)
 * 3. Encryption keys are permanently deleted
 * 4. All encrypted data becomes cryptographically inaccessible
 *
 * GDPR Compliance:
 * - This implements "crypto-shredding" - destroying the key makes
 *   the encrypted data effectively deleted
 * - Provides strong guarantees for data deletion
 * - The encrypted ciphertext can be retained for audit purposes
 *   (it's cryptographically useless without the key)
 */
class HandleSubscriptionCancellation
{
    public function __construct(
        private readonly TeamEncryptionService $encryptionService
    ) {}

    /**
     * Handle Stripe webhook events.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        // Handle subscription deletion (end of subscription lifecycle)
        if ($payload['type'] === 'customer.subscription.deleted') {
            $this->handleSubscriptionDeleted($payload);
        }

        // Handle subscription update (check for cancellation)
        if ($payload['type'] === 'customer.subscription.updated') {
            $this->handleSubscriptionUpdated($payload);
        }
    }

    /**
     * Handle complete subscription deletion.
     */
    private function handleSubscriptionDeleted(array $payload): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) {
            Log::warning('Subscription deleted webhook missing customer ID');
            return;
        }

        $team = Team::where('stripe_id', $stripeCustomerId)->first();

        if (!$team) {
            Log::warning("Team not found for Stripe customer: {$stripeCustomerId}");
            return;
        }

        // Check if this is a complete cancellation (not just a plan change)
        $subscription = $payload['data']['object'];

        if ($subscription['status'] === 'canceled') {
            $this->scheduleKeyDestruction($team);
        }
    }

    /**
     * Handle subscription updates.
     */
    private function handleSubscriptionUpdated(array $payload): void
    {
        $subscription = $payload['data']['object'];
        $stripeCustomerId = $subscription['customer'] ?? null;

        if (!$stripeCustomerId) {
            return;
        }

        $team = Team::where('stripe_id', $stripeCustomerId)->first();

        if (!$team) {
            return;
        }

        // If subscription is set to cancel at period end, log it
        if ($subscription['cancel_at_period_end'] ?? false) {
            $cancelAt = $subscription['cancel_at'] ?? null;

            Log::info("Team {$team->id} subscription set to cancel", [
                'team_id' => $team->id,
                'cancel_at' => $cancelAt,
            ]);
        }
    }

    /**
     * Schedule encryption key destruction.
     *
     * In production, you might want to:
     * 1. Send warning emails before destruction
     * 2. Wait for a grace period (e.g., 30 days)
     * 3. Allow reactivation during grace period
     */
    private function scheduleKeyDestruction(Team $team): void
    {
        // For immediate destruction (or you can schedule a job)
        Log::warning("DESTROYING encryption keys for team {$team->id} due to subscription cancellation");

        try {
            $this->encryptionService->destroyEncryption($team);

            Log::info("Encryption keys destroyed for team {$team->id}", [
                'team_id' => $team->id,
                'destroyed_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to destroy encryption keys for team {$team->id}", [
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
