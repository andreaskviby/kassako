<?php

namespace App\Http\Controllers;

use App\Models\FortnoxConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FortnoxWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        if (! $this->verifySignature($payload, $signature)) {
            Log::warning('Fortnox webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->all();

        Log::info('Fortnox webhook received', [
            'event' => $event,
            'data' => $data,
        ]);

        return match ($event) {
            'invited_user' => $this->handleInvitedUser($data),
            'purchase' => $this->handlePurchase($data),
            'rating' => $this->handleRating($data),
            'deactivation' => $this->handleDeactivation($data),
            'license_expired' => $this->handleLicenseExpired($data),
            'client_credentials_consent' => $this->handleClientCredentialsConsent($data),
            'client_credentials_consent_revoked' => $this->handleClientCredentialsConsentRevoked($data),
            default => $this->handleUnknownEvent($event, $data),
        };
    }

    private function verifySignature(string $payload, ?string $signature): bool
    {
        $secret = config('fortnox.webhook_secret');

        if (empty($secret)) {
            return true;
        }

        if (empty($signature)) {
            return false;
        }

        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expectedSignature, $signature);
    }

    private function handleInvitedUser(array $data): \Illuminate\Http\JsonResponse
    {
        Log::info('Fortnox: User invited', [
            'email' => $data['email'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'tenant_id' => $data['tenant_id'] ?? null,
            'user_type' => $data['user_type'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handlePurchase(array $data): \Illuminate\Http\JsonResponse
    {
        Log::info('Fortnox: Integration purchased', [
            'tenant_id' => $data['tenant_id'] ?? null,
            'organization_number' => $data['organization_number'] ?? null,
            'license_count' => $data['license_count'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleRating(array $data): \Illuminate\Http\JsonResponse
    {
        Log::info('Fortnox: Rating received', [
            'user_id' => $data['user_id'] ?? null,
            'tenant_id' => $data['tenant_id'] ?? null,
            'rating' => $data['rating'] ?? null,
            'review' => $data['review'] ?? null,
            'average_rating' => $data['average_rating'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleDeactivation(array $data): \Illuminate\Http\JsonResponse
    {
        $refreshToken = $data['refresh_token'] ?? null;

        Log::info('Fortnox: Integration deactivated', [
            'refresh_token_prefix' => $refreshToken ? substr($refreshToken, 0, 10) . '...' : null,
        ]);

        if ($refreshToken) {
            $connection = FortnoxConnection::where('refresh_token', $refreshToken)->first();

            if ($connection) {
                $connection->update([
                    'is_active' => false,
                    'access_token' => null,
                    'refresh_token' => null,
                    'sync_status' => 'deactivated',
                ]);

                Log::info('Fortnox: Connection deactivated for team', [
                    'team_id' => $connection->team_id,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleLicenseExpired(array $data): \Illuminate\Http\JsonResponse
    {
        Log::warning('Fortnox: License expired', [
            'tenant_id' => $data['tenant_id'] ?? null,
            'license_count' => $data['license_count'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleClientCredentialsConsent(array $data): \Illuminate\Http\JsonResponse
    {
        Log::info('Fortnox: Client credentials consent given', [
            'tenant_id' => $data['tenant_id'] ?? null,
            'scopes' => $data['scopes'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleClientCredentialsConsentRevoked(array $data): \Illuminate\Http\JsonResponse
    {
        Log::info('Fortnox: Client credentials consent revoked', [
            'tenant_id' => $data['tenant_id'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function handleUnknownEvent(string $event, array $data): \Illuminate\Http\JsonResponse
    {
        Log::warning('Fortnox: Unknown webhook event', [
            'event' => $event,
            'data' => $data,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
