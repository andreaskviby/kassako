<?php

namespace App\Http\Controllers;

use App\Jobs\SyncFortnoxData;
use App\Models\FortnoxConnection;
use App\Services\Fortnox\FortnoxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FortnoxController extends Controller
{
    public function connect(Request $request)
    {
        $team = Auth::user()->currentTeam;

        if ($team->hasFortnoxConnected()) {
            return redirect()->route('dashboard')
                ->with('message', 'Fortnox är redan kopplat.');
        }

        $state = Str::random(40);
        session(['fortnox_oauth_state' => $state]);

        $authUrl = FortnoxService::getAuthorizationUrl($state);

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        if ($request->state !== session('fortnox_oauth_state')) {
            return redirect()->route('dashboard')
                ->with('error', 'Ogiltig OAuth-förfrågan. Försök igen.');
        }

        if ($request->has('error')) {
            return redirect()->route('dashboard')
                ->with('error', 'Fortnox-anslutningen avbröts: ' . $request->error_description);
        }

        $team = Auth::user()->currentTeam;

        try {
            $tokens = FortnoxService::exchangeCodeForTokens($request->code);

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

            SyncFortnoxData::dispatch($team);

            return redirect()->route('dashboard')
                ->with('success', 'Fortnox kopplat! Din data synkroniseras nu...');
        } catch (\Exception $e) {
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
