<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\Encryption\DataEncryptorService;
use App\Services\Encryption\KeyDerivationService;
use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Encryption Controller
 *
 * Handles encryption setup, unlock, lock, and passphrase changes.
 * This is the user-facing interface for the zero-knowledge encryption system.
 */
class EncryptionController extends Controller
{
    public function __construct(
        private readonly TeamEncryptionService $encryptionService,
        private readonly DataEncryptorService $dataEncryptorService,
        private readonly KeyDerivationService $keyDerivationService
    ) {}

    /**
     * Show the encryption setup page.
     */
    public function showSetup(Request $request): View|RedirectResponse
    {
        $team = $request->user()->currentTeam;

        if ($team->hasEncryptionInitialized()) {
            return redirect()->route('encryption.unlock');
        }

        return view('encryption.setup', [
            'team' => $team,
        ]);
    }

    /**
     * Initialize encryption for the team.
     */
    public function setup(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'passphrase' => ['required', 'string', 'min:12', 'max:128'],
            'passphrase_confirmation' => ['required', 'same:passphrase'],
        ]);

        $team = $request->user()->currentTeam;

        if ($team->hasEncryptionInitialized()) {
            return $this->errorResponse('Encryption is already set up for this team.');
        }

        // Validate passphrase strength
        $validation = $this->keyDerivationService->validatePassphraseStrength(
            $request->input('passphrase')
        );

        if (!$validation['valid']) {
            return $this->errorResponse(implode(' ', $validation['errors']));
        }

        try {
            // Initialize encryption
            $this->encryptionService->initializeEncryption(
                $team,
                $request->input('passphrase')
            );

            // Generate session ID for this encryption session
            $sessionId = $this->generateSessionId();
            session(['encryption_session_id' => $sessionId]);

            // Unlock encryption for the current session
            $this->encryptionService->unlockEncryption(
                $team,
                $request->input('passphrase'),
                $sessionId
            );

            // Encrypt any existing data
            $stats = $this->dataEncryptorService->encryptExistingData($team, $sessionId);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Encryption initialized successfully.',
                    'encrypted_records' => $stats,
                ]);
            }

            return redirect()
                ->route('dashboard')
                ->with('success', 'Encryption has been set up. Your data is now protected.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to set up encryption: ' . $e->getMessage());
        }
    }

    /**
     * Show the unlock page.
     */
    public function showUnlock(Request $request): View|RedirectResponse
    {
        $team = $request->user()->currentTeam;

        if (!$team->hasEncryptionInitialized()) {
            return redirect()->route('encryption.setup');
        }

        $sessionId = session('encryption_session_id', $this->generateSessionId());
        session(['encryption_session_id' => $sessionId]);

        if ($this->encryptionService->isUnlocked($team, $sessionId)) {
            return redirect()->route('dashboard');
        }

        return view('encryption.unlock', [
            'team' => $team,
        ]);
    }

    /**
     * Unlock encryption for the session.
     */
    public function unlock(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'passphrase' => ['required', 'string'],
        ]);

        $team = $request->user()->currentTeam;

        if (!$team->hasEncryptionInitialized()) {
            return $this->errorResponse('Encryption has not been set up for this team.');
        }

        $sessionId = session('encryption_session_id', $this->generateSessionId());
        session(['encryption_session_id' => $sessionId]);

        $unlocked = $this->encryptionService->unlockEncryption(
            $team,
            $request->input('passphrase'),
            $sessionId
        );

        if (!$unlocked) {
            return $this->errorResponse('Incorrect passphrase. Please try again.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Encryption unlocked successfully.',
            ]);
        }

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Encryption unlocked.');
    }

    /**
     * Lock encryption (clear session keys).
     */
    public function lock(Request $request): RedirectResponse|JsonResponse
    {
        $team = $request->user()->currentTeam;
        $sessionId = session('encryption_session_id');

        if ($sessionId) {
            $this->encryptionService->lockEncryption($team, $sessionId);
        }

        session()->forget('encryption_session_id');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Encryption locked.',
            ]);
        }

        return redirect()
            ->route('encryption.unlock')
            ->with('info', 'Encryption has been locked.');
    }

    /**
     * Show change passphrase form.
     */
    public function showChangePassphrase(Request $request): View|RedirectResponse
    {
        $team = $request->user()->currentTeam;
        $sessionId = session('encryption_session_id');

        if (!$sessionId || !$this->encryptionService->isUnlocked($team, $sessionId)) {
            return redirect()->route('encryption.unlock')
                ->with('warning', 'Please unlock encryption first.');
        }

        return view('encryption.change-passphrase', [
            'team' => $team,
        ]);
    }

    /**
     * Change the encryption passphrase.
     */
    public function changePassphrase(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'current_passphrase' => ['required', 'string'],
            'new_passphrase' => ['required', 'string', 'min:12', 'max:128'],
            'new_passphrase_confirmation' => ['required', 'same:new_passphrase'],
        ]);

        $team = $request->user()->currentTeam;
        $sessionId = session('encryption_session_id');

        if (!$sessionId) {
            return $this->errorResponse('Encryption is not unlocked.');
        }

        // Validate new passphrase strength
        $validation = $this->keyDerivationService->validatePassphraseStrength(
            $request->input('new_passphrase')
        );

        if (!$validation['valid']) {
            return $this->errorResponse(implode(' ', $validation['errors']));
        }

        try {
            $success = $this->encryptionService->changePassphrase(
                $team,
                $request->input('current_passphrase'),
                $request->input('new_passphrase'),
                $sessionId
            );

            if (!$success) {
                return $this->errorResponse('Current passphrase is incorrect.');
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Passphrase changed successfully.',
                ]);
            }

            return redirect()
                ->route('settings.security')
                ->with('success', 'Your encryption passphrase has been changed.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change passphrase: ' . $e->getMessage());
        }
    }

    /**
     * Get encryption status.
     */
    public function status(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $sessionId = session('encryption_session_id');

        return response()->json([
            'initialized' => $team->hasEncryptionInitialized(),
            'unlocked' => $sessionId && $this->encryptionService->isUnlocked($team, $sessionId),
        ]);
    }

    /**
     * Create a session token for background jobs.
     */
    public function createSessionToken(Request $request): JsonResponse
    {
        $request->validate([
            'purpose' => ['required', 'string', 'in:sync,export,report'],
            'expires_in_minutes' => ['integer', 'min:5', 'max:60'],
        ]);

        $team = $request->user()->currentTeam;
        $sessionId = session('encryption_session_id');

        if (!$sessionId || !$this->encryptionService->isUnlocked($team, $sessionId)) {
            return response()->json([
                'success' => false,
                'message' => 'Encryption must be unlocked to create session tokens.',
            ], 403);
        }

        $tokenId = $this->encryptionService->createSessionToken(
            $team,
            $sessionId,
            $request->input('purpose'),
            $request->input('expires_in_minutes', 60)
        );

        return response()->json([
            'success' => true,
            'token_id' => $tokenId,
            'expires_in_minutes' => $request->input('expires_in_minutes', 60),
        ]);
    }

    /**
     * Generate a unique session ID.
     */
    private function generateSessionId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Return an error response.
     */
    private function errorResponse(string $message): RedirectResponse|JsonResponse
    {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors(['passphrase' => $message]);
    }
}
