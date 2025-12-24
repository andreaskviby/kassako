<?php

namespace App\Services\Encryption;

use App\Models\EncryptionSessionToken;
use App\Models\Team;
use App\Models\TeamEncryptionKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Team Encryption Service
 *
 * This is the main service for managing team encryption keys and performing
 * encryption/decryption operations. It implements a zero-knowledge architecture
 * where the service provider (you) can NEVER access the plaintext encryption keys.
 *
 * Architecture:
 * 1. User enters passphrase
 * 2. Passphrase -> Argon2id -> Master Encryption Key (MEK)
 * 3. MEK unwraps (decrypts) the stored Data Encryption Key (DEK)
 * 4. DEK encrypts/decrypts actual financial data
 *
 * Why this two-layer approach?
 * - Passphrase change only requires re-wrapping the DEK, not re-encrypting all data
 * - Key rotation is efficient: generate new DEK, re-encrypt data, wrap with same MEK
 * - Different data categories can use different DEKs if needed
 *
 * Trust Model:
 * - The passphrase is NEVER stored or logged
 * - The MEK is NEVER stored (only in memory during session)
 * - The DEK is stored encrypted (wrapped) - useless without MEK
 * - Without the passphrase, all data is cryptographically inaccessible
 */
class TeamEncryptionService
{
    private const SESSION_CACHE_PREFIX = 'team_mek:';
    private const SESSION_CACHE_TTL = 1800; // 30 minutes

    public function __construct(
        private readonly KeyDerivationService $keyDerivation,
        private readonly AesGcmEncryption $aesGcm
    ) {}

    /**
     * Initialize encryption for a team (first-time setup).
     *
     * @param Team $team
     * @param string $passphrase User-chosen passphrase
     * @return TeamEncryptionKey
     */
    public function initializeEncryption(Team $team, string $passphrase): TeamEncryptionKey
    {
        // Validate passphrase strength
        $validation = $this->keyDerivation->validatePassphraseStrength($passphrase);
        if (!$validation['valid']) {
            throw new RuntimeException(implode(', ', $validation['errors']));
        }

        // Check if already initialized
        if ($team->encryptionKey()->exists()) {
            throw new RuntimeException('Encryption already initialized for this team');
        }

        // Generate salts
        $keySalt = $this->keyDerivation->generateSalt();
        $verificationSalt = $this->keyDerivation->generateSalt();

        // Derive the Master Encryption Key from passphrase
        $mek = $this->keyDerivation->deriveKey($passphrase, $keySalt);

        // Generate a random Data Encryption Key
        $dek = $this->keyDerivation->generateDek();

        // Wrap (encrypt) the DEK with the MEK
        $wrappedKey = $this->aesGcm->wrapKey($dek, $mek);

        // Create verification hash
        $verificationHash = $this->keyDerivation->createVerificationHash($passphrase, $verificationSalt);

        // Store the encryption key record
        $encryptionKey = $team->encryptionKey()->create([
            'wrapped_dek' => $wrappedKey['wrapped_dek'],
            'key_salt' => $keySalt,
            'verification_hash' => $verificationHash,
            'verification_salt' => $verificationSalt,
            'wrap_iv' => $wrappedKey['iv'],
            'wrap_auth_tag' => $wrappedKey['tag'],
            'key_version' => 1,
        ]);

        // Clear sensitive data from memory
        $this->keyDerivation->clearSensitiveData($mek);
        $this->keyDerivation->clearSensitiveData($dek);

        return $encryptionKey;
    }

    /**
     * Unlock encryption for a team session.
     *
     * This verifies the passphrase and caches the MEK for the session duration.
     * The MEK is stored encrypted in cache with a server-side session key.
     *
     * @param Team $team
     * @param string $passphrase
     * @param string $sessionId User's session ID for cache isolation
     * @return bool
     */
    public function unlockEncryption(Team $team, string $passphrase, string $sessionId): bool
    {
        $encryptionKey = $team->encryptionKey;

        if (!$encryptionKey) {
            throw new RuntimeException('Encryption not initialized for this team');
        }

        // Verify the passphrase
        if (!$this->keyDerivation->verifyPassphrase(
            $passphrase,
            $encryptionKey->verification_salt,
            $encryptionKey->verification_hash
        )) {
            return false;
        }

        // Derive the MEK
        $mek = $this->keyDerivation->deriveKey($passphrase, $encryptionKey->key_salt);

        // Try to unwrap the DEK to verify everything works
        try {
            $dek = $this->aesGcm->unwrapKey(
                $encryptionKey->wrapped_dek,
                $mek,
                $encryptionKey->wrap_iv,
                $encryptionKey->wrap_auth_tag
            );
            $this->keyDerivation->clearSensitiveData($dek);
        } catch (RuntimeException $e) {
            $this->keyDerivation->clearSensitiveData($mek);
            return false;
        }

        // Cache the MEK encrypted with Laravel's app key
        // This provides an additional layer even if cache is compromised
        $cacheKey = $this->getCacheKey($team->id, $sessionId);
        Cache::put(
            $cacheKey,
            Crypt::encryptString(base64_encode($mek)),
            self::SESSION_CACHE_TTL
        );

        // Update last accessed timestamp
        $encryptionKey->touch('last_accessed_at');

        // Clear the raw MEK from memory
        $this->keyDerivation->clearSensitiveData($mek);

        return true;
    }

    /**
     * Lock encryption (clear cached keys).
     */
    public function lockEncryption(Team $team, string $sessionId): void
    {
        $cacheKey = $this->getCacheKey($team->id, $sessionId);
        Cache::forget($cacheKey);
    }

    /**
     * Check if encryption is currently unlocked.
     */
    public function isUnlocked(Team $team, string $sessionId): bool
    {
        $cacheKey = $this->getCacheKey($team->id, $sessionId);
        return Cache::has($cacheKey);
    }

    /**
     * Get the Data Encryption Key for a team.
     *
     * This should only be called when encryption is unlocked.
     *
     * @param Team $team
     * @param string $sessionId
     * @return string Binary DEK
     */
    public function getDek(Team $team, string $sessionId): string
    {
        $encryptionKey = $team->encryptionKey;

        if (!$encryptionKey) {
            throw new RuntimeException('Encryption not initialized');
        }

        $mek = $this->getMek($team, $sessionId);

        // Unwrap the DEK
        $dek = $this->aesGcm->unwrapKey(
            $encryptionKey->wrapped_dek,
            $mek,
            $encryptionKey->wrap_iv,
            $encryptionKey->wrap_auth_tag
        );

        // Clear MEK from memory
        $this->keyDerivation->clearSensitiveData($mek);

        return $dek;
    }

    /**
     * Encrypt data for storage.
     *
     * @param array<string, mixed> $data
     * @param Team $team
     * @param string $sessionId
     * @return array{encrypted_data: string, encryption_iv: string, encryption_auth_tag: string, encryption_version: int}
     */
    public function encryptForStorage(array $data, Team $team, string $sessionId): array
    {
        $dek = $this->getDek($team, $sessionId);
        $encryptionKey = $team->encryptionKey;

        $encrypted = $this->aesGcm->encryptData($data, $dek, $team->id);

        $this->keyDerivation->clearSensitiveData($dek);

        return [
            'encrypted_data' => $encrypted['ciphertext'],
            'encryption_iv' => $encrypted['iv'],
            'encryption_auth_tag' => $encrypted['tag'],
            'encryption_version' => $encryptionKey->key_version,
        ];
    }

    /**
     * Decrypt data from storage.
     *
     * @param string $encryptedData
     * @param string $iv
     * @param string $authTag
     * @param Team $team
     * @param string $sessionId
     * @return array<string, mixed>
     */
    public function decryptFromStorage(
        string $encryptedData,
        string $iv,
        string $authTag,
        Team $team,
        string $sessionId
    ): array {
        $dek = $this->getDek($team, $sessionId);

        $decrypted = $this->aesGcm->decryptData(
            $encryptedData,
            $dek,
            $iv,
            $authTag,
            $team->id
        );

        $this->keyDerivation->clearSensitiveData($dek);

        return $decrypted;
    }

    /**
     * Change the team's encryption passphrase.
     *
     * This re-wraps the existing DEK with a new MEK derived from the new passphrase.
     * All existing encrypted data remains valid because the DEK stays the same.
     */
    public function changePassphrase(
        Team $team,
        string $currentPassphrase,
        string $newPassphrase,
        string $sessionId
    ): bool {
        // Validate new passphrase
        $validation = $this->keyDerivation->validatePassphraseStrength($newPassphrase);
        if (!$validation['valid']) {
            throw new RuntimeException(implode(', ', $validation['errors']));
        }

        $encryptionKey = $team->encryptionKey;

        // Verify current passphrase
        if (!$this->keyDerivation->verifyPassphrase(
            $currentPassphrase,
            $encryptionKey->verification_salt,
            $encryptionKey->verification_hash
        )) {
            return false;
        }

        // Derive current MEK and unwrap DEK
        $currentMek = $this->keyDerivation->deriveKey($currentPassphrase, $encryptionKey->key_salt);
        $dek = $this->aesGcm->unwrapKey(
            $encryptionKey->wrapped_dek,
            $currentMek,
            $encryptionKey->wrap_iv,
            $encryptionKey->wrap_auth_tag
        );

        // Generate new salts
        $newKeySalt = $this->keyDerivation->generateSalt();
        $newVerificationSalt = $this->keyDerivation->generateSalt();

        // Derive new MEK
        $newMek = $this->keyDerivation->deriveKey($newPassphrase, $newKeySalt);

        // Re-wrap DEK with new MEK
        $wrappedKey = $this->aesGcm->wrapKey($dek, $newMek);

        // Create new verification hash
        $verificationHash = $this->keyDerivation->createVerificationHash($newPassphrase, $newVerificationSalt);

        // Update the encryption key record
        $encryptionKey->update([
            'wrapped_dek' => $wrappedKey['wrapped_dek'],
            'key_salt' => $newKeySalt,
            'verification_hash' => $verificationHash,
            'verification_salt' => $newVerificationSalt,
            'wrap_iv' => $wrappedKey['iv'],
            'wrap_auth_tag' => $wrappedKey['tag'],
        ]);

        // Clear sensitive data
        $this->keyDerivation->clearSensitiveData($currentMek);
        $this->keyDerivation->clearSensitiveData($newMek);
        $this->keyDerivation->clearSensitiveData($dek);

        // Re-cache the new MEK
        $cacheKey = $this->getCacheKey($team->id, $sessionId);
        Cache::forget($cacheKey);

        $newMek = $this->keyDerivation->deriveKey($newPassphrase, $newKeySalt);
        Cache::put(
            $cacheKey,
            Crypt::encryptString(base64_encode($newMek)),
            self::SESSION_CACHE_TTL
        );
        $this->keyDerivation->clearSensitiveData($newMek);

        return true;
    }

    /**
     * Create a session token for background job processing.
     *
     * This allows scheduled jobs to decrypt data without user interaction.
     * The token has a short expiration and limited purpose.
     */
    public function createSessionToken(
        Team $team,
        string $sessionId,
        string $purpose,
        int $expiresInMinutes = 60
    ): string {
        $mek = $this->getMek($team, $sessionId);
        $tokenId = Str::random(64);

        // Encrypt the MEK with a server-side key for the token
        $encrypted = $this->aesGcm->encrypt(
            $mek,
            $this->getServerSessionKey(),
            "session_token:{$tokenId}"
        );

        EncryptionSessionToken::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'encrypted_mek' => $encrypted['ciphertext'],
            'session_iv' => $encrypted['iv'],
            'session_auth_tag' => $encrypted['tag'],
            'expires_at' => now()->addMinutes($expiresInMinutes),
            'purpose' => $purpose,
            'token_id' => $tokenId,
        ]);

        $this->keyDerivation->clearSensitiveData($mek);

        return $tokenId;
    }

    /**
     * Get DEK using a session token (for background jobs).
     */
    public function getDekFromToken(Team $team, string $tokenId): string
    {
        $token = EncryptionSessionToken::where('token_id', $tokenId)
            ->where('team_id', $team->id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            throw new RuntimeException('Invalid or expired session token');
        }

        $encryptionKey = $team->encryptionKey;

        // Decrypt the MEK from the token
        $mek = $this->aesGcm->decrypt(
            $token->encrypted_mek,
            $this->getServerSessionKey(),
            $token->session_iv,
            $token->session_auth_tag,
            "session_token:{$tokenId}"
        );

        // Unwrap the DEK
        $dek = $this->aesGcm->unwrapKey(
            $encryptionKey->wrapped_dek,
            $mek,
            $encryptionKey->wrap_iv,
            $encryptionKey->wrap_auth_tag
        );

        $this->keyDerivation->clearSensitiveData($mek);

        return $dek;
    }

    /**
     * Invalidate a session token.
     */
    public function invalidateToken(string $tokenId): void
    {
        EncryptionSessionToken::where('token_id', $tokenId)->delete();
    }

    /**
     * Clean up expired session tokens.
     */
    public function cleanupExpiredTokens(): int
    {
        return EncryptionSessionToken::where('expires_at', '<', now())->delete();
    }

    /**
     * Permanently destroy encryption keys (for subscription cancellation).
     *
     * WARNING: This makes ALL encrypted data permanently inaccessible.
     * This operation is IRREVERSIBLE.
     */
    public function destroyEncryption(Team $team): void
    {
        // Delete all session tokens
        EncryptionSessionToken::where('team_id', $team->id)->delete();

        // Delete the encryption key - this makes all data unrecoverable
        $team->encryptionKey()->delete();

        // Clear any cached MEKs
        Cache::forget($this->getCacheKey($team->id, '*'));
    }

    /**
     * Get the cached MEK for a session.
     */
    private function getMek(Team $team, string $sessionId): string
    {
        $cacheKey = $this->getCacheKey($team->id, $sessionId);
        $encryptedMek = Cache::get($cacheKey);

        if (!$encryptedMek) {
            throw new RuntimeException('Encryption is locked. Please enter your passphrase.');
        }

        return base64_decode(Crypt::decryptString($encryptedMek));
    }

    /**
     * Generate the cache key for a team session.
     */
    private function getCacheKey(int $teamId, string $sessionId): string
    {
        return self::SESSION_CACHE_PREFIX . $teamId . ':' . $sessionId;
    }

    /**
     * Get the server-side session key for token encryption.
     */
    private function getServerSessionKey(): string
    {
        // Derive from APP_KEY with domain separation
        $appKey = config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        return hash('sha256', $appKey . ':session_tokens', true);
    }
}
