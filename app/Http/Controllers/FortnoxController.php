<?php

namespace App\Http\Controllers;

use App\Jobs\SyncFortnoxData;
use App\Models\FortnoxConnection;
use App\Services\Encryption\TeamEncryptionService;
use App\Services\Fortnox\FortnoxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FortnoxController extends Controller
{
    public function __construct(
        private readonly TeamEncryptionService $encryptionService
    ) {}

    public function connect(Request $request)
    {
        $team = Auth::user()->currentTeam;

        if ($team->hasFortnoxConnected()) {
            return redirect()->route('dashboard')
                ->with('message', 'Fortnox är redan kopplat.');
        }

        // Check if encryption is set up before allowing Fortnox connection
        if (!$team->hasEncryptionInitialized()) {
            return redirect()->route('encryption.setup')
                ->with('info', 'För att skydda din finansiella data behöver du först skapa en krypteringsnyckel.');
        }

        // Check if encryption is unlocked
        $sessionId = session('encryption_session_id');
        if (!$sessionId || !$this->encryptionService->isUnlocked($team, $sessionId)) {
            return redirect()->route('encryption.unlock')
                ->with('info', 'Lås upp krypteringen för att koppla Fortnox.');
        }

        $state = Str::random(40);
        session(['fortnox_oauth_state' => $state]);

        $authUrl = FortnoxService::getAuthorizationUrl($state);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        Log::info('Fortnox callback received', [
            'state' => $request->state,
            'session_state' => session('fortnox_oauth_state'),
            'has_code' => $request->has('code'),
            'has_error' => $request->has('error'),
        ]);

        if ($request->state !== session('fortnox_oauth_state')) {
            Log::warning('Fortnox OAuth state mismatch', [
                'request_state' => $request->state,
                'session_state' => session('fortnox_oauth_state'),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Ogiltig OAuth-förfrågan. Försök igen.');
        }

        if ($request->has('error')) {
            Log::error('Fortnox OAuth error', [
                'error' => $request->error,
                'description' => $request->error_description,
            ]);

            $errorMessage = match ($request->error) {
                'error_missing_app_license' => 'Ditt Fortnox-konto saknar nödvändiga licenser för att använda CashDash. Kontrollera att du har rätt abonnemang i Fortnox.',
                'access_denied' => 'Du nekade åtkomst till Fortnox. Försök igen om du vill ansluta.',
                'invalid_scope' => 'CashDash begär behörigheter som ditt Fortnox-konto inte stödjer.',
                default => 'Fortnox-anslutningen misslyckades: ' . ($request->error_description ?? $request->error),
            };

            return redirect()->route('dashboard')->with('error', $errorMessage);
        }

        $team = Auth::user()->currentTeam;

        try {
            Log::info('Exchanging Fortnox code for tokens');
            $tokens = FortnoxService::exchangeCodeForTokens($request->code);
            Log::info('Fortnox tokens received', ['expires_in' => $tokens['expires_in'] ?? 'unknown']);

            FortnoxConnection::updateOrCreate(
                ['team_id' => $team->id],
                [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'],
                    'token_expires_at' => now()->addSeconds($tokens['expires_in']),
                    'is_active' => true,
                    'sync_status' => 'pending',
                ]
            );

            Log::info('Fortnox connection saved', ['team_id' => $team->id]);
            SyncFortnoxData::dispatch($team);

            return redirect()->route('dashboard')
                ->with('success', 'Fortnox kopplat! Din data synkroniseras nu...');
        } catch (\Exception $e) {
            Log::error('Fortnox connection failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Kunde inte koppla Fortnox: ' . $e->getMessage());
        }
    }

    public function disconnect(Request $request)
    {
        $team = Auth::user()->currentTeam;

        $connection = $team->fortnoxConnection;

        if ($connection) {
            $connection->update([
                'is_active' => false,
                'access_token' => null,
                'refresh_token' => null,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Fortnox-kopplingen är borttagen.');
    }
}
