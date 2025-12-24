<?php

namespace App\Traits;

use App\Models\Team;
use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Support\Facades\App;
use RuntimeException;

/**
 * Trait for Models with Encrypted Data
 *
 * This trait provides encryption/decryption capabilities for Eloquent models.
 * It handles the transparent encryption and decryption of sensitive fields.
 *
 * Usage:
 * 1. Add the trait to your model
 * 2. Define $encryptedFields array with fields to encrypt
 * 3. The trait will automatically encrypt on save and decrypt on load
 *
 * Note: The encryption requires an active session with unlocked encryption.
 * For background jobs, use session tokens.
 */
trait HasEncryptedData
{
    /**
     * Boot the trait.
     */
    public static function bootHasEncryptedData(): void
    {
        static::saving(function ($model) {
            if ($model->shouldEncrypt()) {
                $model->encryptSensitiveData();
            }
        });

        static::retrieved(function ($model) {
            if ($model->is_encrypted) {
                $model->markAsEncrypted();
            }
        });
    }

    /**
     * Get the fields that should be encrypted.
     *
     * Override this in your model to specify which fields to encrypt.
     *
     * @return array<string>
     */
    abstract public function getEncryptedFields(): array;

    /**
     * Get the team that owns this model.
     */
    abstract public function getEncryptionTeam(): Team;

    /**
     * Determine if this model should be encrypted.
     */
    protected function shouldEncrypt(): bool
    {
        // Don't re-encrypt if already encrypted
        if ($this->is_encrypted && !$this->isDirty($this->getEncryptedFields())) {
            return false;
        }

        return true;
    }

    /**
     * Check if the model has decrypted data available.
     */
    protected bool $decryptedDataAvailable = false;

    /**
     * Cache for decrypted data.
     */
    protected ?array $decryptedDataCache = null;

    /**
     * Mark the model as having encrypted data.
     */
    protected function markAsEncrypted(): void
    {
        $this->decryptedDataAvailable = false;
        $this->decryptedDataCache = null;
    }

    /**
     * Encrypt sensitive data before saving.
     */
    protected function encryptSensitiveData(): void
    {
        $service = App::make(TeamEncryptionService::class);
        $team = $this->getEncryptionTeam();
        $sessionId = $this->getEncryptionSessionId();

        $dataToEncrypt = [];
        foreach ($this->getEncryptedFields() as $field) {
            if (isset($this->attributes[$field])) {
                $dataToEncrypt[$field] = $this->attributes[$field];
            }
        }

        if (empty($dataToEncrypt)) {
            return;
        }

        $encrypted = $service->encryptForStorage($dataToEncrypt, $team, $sessionId);

        $this->attributes['encrypted_data'] = $encrypted['encrypted_data'];
        $this->attributes['encryption_iv'] = $encrypted['encryption_iv'];
        $this->attributes['encryption_auth_tag'] = $encrypted['encryption_auth_tag'];
        $this->attributes['encryption_version'] = $encrypted['encryption_version'];
        $this->attributes['is_encrypted'] = true;

        // Clear the plaintext fields (they're now in encrypted_data)
        // Keep them as null in the database for querying purposes
        foreach ($this->getEncryptedFields() as $field) {
            if ($this->isNullableField($field)) {
                $this->attributes[$field] = null;
            }
        }
    }

    /**
     * Decrypt sensitive data.
     *
     * @param string|null $sessionId Session ID or token ID for background jobs
     * @param string|null $tokenId Token ID for background job access
     */
    public function decryptData(?string $sessionId = null, ?string $tokenId = null): array
    {
        if (!$this->is_encrypted || !$this->encrypted_data) {
            return [];
        }

        if ($this->decryptedDataAvailable && $this->decryptedDataCache !== null) {
            return $this->decryptedDataCache;
        }

        $service = App::make(TeamEncryptionService::class);
        $team = $this->getEncryptionTeam();

        if ($tokenId) {
            // Background job access via token
            $dek = $service->getDekFromToken($team, $tokenId);
            $aes = App::make(\App\Services\Encryption\AesGcmEncryption::class);

            $decrypted = $aes->decryptData(
                $this->encrypted_data,
                $dek,
                $this->encryption_iv,
                $this->encryption_auth_tag,
                $team->id
            );

            // Clear DEK from memory
            $keyDerivation = App::make(\App\Services\Encryption\KeyDerivationService::class);
            $keyDerivation->clearSensitiveData($dek);
        } else {
            // Interactive session access
            $sessionId = $sessionId ?? $this->getEncryptionSessionId();
            $decrypted = $service->decryptFromStorage(
                $this->encrypted_data,
                $this->encryption_iv,
                $this->encryption_auth_tag,
                $team,
                $sessionId
            );
        }

        $this->decryptedDataAvailable = true;
        $this->decryptedDataCache = $decrypted;

        return $decrypted;
    }

    /**
     * Get a specific decrypted field.
     */
    public function getDecryptedField(string $field, ?string $sessionId = null, ?string $tokenId = null): mixed
    {
        $decrypted = $this->decryptData($sessionId, $tokenId);

        return $decrypted[$field] ?? null;
    }

    /**
     * Get the encryption session ID.
     */
    protected function getEncryptionSessionId(): string
    {
        // Try to get from session, fallback to a session token if available
        if (session()->has('encryption_session_id')) {
            return session()->get('encryption_session_id');
        }

        // For API requests, use request ID
        if (request()->hasHeader('X-Encryption-Session')) {
            return request()->header('X-Encryption-Session');
        }

        throw new RuntimeException('No encryption session available. Please unlock encryption first.');
    }

    /**
     * Check if a field is nullable in the database.
     */
    protected function isNullableField(string $field): bool
    {
        // Define non-nullable fields that should keep placeholder values
        $nonNullable = $this->getNonNullableEncryptedFields();

        return !in_array($field, $nonNullable, true);
    }

    /**
     * Get non-nullable encrypted fields (override in model if needed).
     *
     * @return array<string>
     */
    protected function getNonNullableEncryptedFields(): array
    {
        return [];
    }

    /**
     * Prepare the model for serialization (API responses).
     *
     * This ensures encrypted data is decrypted before serialization.
     */
    public function toArrayWithDecryption(?string $sessionId = null, ?string $tokenId = null): array
    {
        $array = $this->toArray();

        if ($this->is_encrypted) {
            $decrypted = $this->decryptData($sessionId, $tokenId);
            foreach ($decrypted as $field => $value) {
                $array[$field] = $value;
            }
        }

        // Remove encryption metadata from output
        unset(
            $array['encrypted_data'],
            $array['encryption_iv'],
            $array['encryption_auth_tag'],
            $array['encryption_version'],
            $array['is_encrypted']
        );

        return $array;
    }
}
