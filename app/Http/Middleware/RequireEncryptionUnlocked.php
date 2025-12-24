<?php

namespace App\Http\Middleware;

use App\Services\Encryption\TeamEncryptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Require Encryption Unlocked Middleware
 *
 * This middleware ensures that encryption is unlocked before
 * accessing routes that require decrypted data.
 *
 * Apply to routes that display or process sensitive financial data:
 * - Dashboard
 * - Invoice lists
 * - Cash flow reports
 * - Customer analytics
 */
class RequireEncryptionUnlocked
{
    public function __construct(
        private readonly TeamEncryptionService $encryptionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $team = $user->currentTeam;

        if (!$team) {
            return redirect()->route('teams.create');
        }

        // Check if encryption is initialized
        if (!$team->hasEncryptionInitialized()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'encryption_not_initialized',
                    'message' => 'Please set up encryption for your team first.',
                ], 403);
            }

            return redirect()->route('encryption.setup');
        }

        // Check if encryption is unlocked
        $sessionId = session('encryption_session_id');

        if (!$sessionId || !$this->encryptionService->isUnlocked($team, $sessionId)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'encryption_locked',
                    'message' => 'Please unlock encryption to access this resource.',
                ], 403);
            }

            return redirect()->route('encryption.unlock')
                ->with('warning', 'Please enter your encryption passphrase to continue.');
        }

        return $next($request);
    }
}
