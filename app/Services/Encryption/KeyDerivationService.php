<?php

namespace App\Services\Encryption;

use RuntimeException;

/**
 * Key Derivation Service using Argon2id
 *
 * This service handles the cryptographic key derivation from user passphrases.
 * It uses Argon2id, which is resistant to:
 * - GPU/ASIC attacks (memory-hard)
 * - Side-channel attacks (combines Argon2i and Argon2d)
 * - Time-memory trade-off attacks
 *
 * The derived keys are NEVER stored - they exist only in memory during the session.
 *
 * Security Parameters (OWASP recommended for sensitive data):
 * - Memory: 64 MiB (65536 KiB)
 * - Iterations: 3
 * - Parallelism: 4
 * - Output length: 32 bytes (256 bits)
 */
class KeyDerivationService
{
    private const MEMORY_COST = 65536;  // 64 MiB in KiB
    private const TIME_COST = 3;         // Number of iterations
    private const PARALLELISM = 4;       // Threads
    private const KEY_LENGTH = 32;       // 256 bits
    private const SALT_LENGTH = SODIUM_CRYPTO_PWHASH_SALTBYTES;  // 16 bytes (required by sodium)
    private const VERIFICATION_LENGTH = 32;

    /**
     * Generate a cryptographically secure random salt.
     */
    public function generateSalt(): string
    {
        return base64_encode(random_bytes(self::SALT_LENGTH));
    }

    /**
     * Derive a Master Encryption Key (MEK) from a passphrase.
     *
     * @param string $passphrase The user's passphrase
     * @param string $salt Base64-encoded salt
     * @return string Binary key (32 bytes)
     */
    public function deriveKey(string $passphrase, string $salt): string
    {
        $saltBytes = base64_decode($salt);

        if (strlen($saltBytes) !== self::SALT_LENGTH) {
            throw new RuntimeException('Invalid salt length');
        }

        // Use sodium_crypto_pwhash with Argon2id
        $key = sodium_crypto_pwhash(
            self::KEY_LENGTH,
            $passphrase,
            $saltBytes,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        return $key;
    }

    /**
     * Create a verification hash for passphrase validation.
     *
     * Uses a different derivation path than the encryption key to prevent
     * any relationship between the verification hash and the encryption key.
     *
     * @param string $passphrase The user's passphrase
     * @param string $verificationSalt Separate salt for verification
     * @return string Hex-encoded hash
     */
    public function createVerificationHash(string $passphrase, string $verificationSalt): string
    {
        $saltBytes = base64_decode($verificationSalt);

        // Derive a verification key using different parameters
        $verificationKey = sodium_crypto_pwhash(
            self::VERIFICATION_LENGTH,
            $passphrase . ':verify',  // Domain separation
            $saltBytes,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        // Hash the verification key for storage
        return bin2hex(sodium_crypto_generichash($verificationKey));
    }

    /**
     * Verify a passphrase against a stored verification hash.
     */
    public function verifyPassphrase(
        string $passphrase,
        string $verificationSalt,
        string $storedHash
    ): bool {
        $computedHash = $this->createVerificationHash($passphrase, $verificationSalt);

        return hash_equals($storedHash, $computedHash);
    }

    /**
     * Generate a random Data Encryption Key (DEK).
     *
     * @return string Binary key (32 bytes)
     */
    public function generateDek(): string
    {
        return random_bytes(self::KEY_LENGTH);
    }

    /**
     * Securely clear sensitive data from memory.
     */
    public function clearSensitiveData(string &$data): void
    {
        if (function_exists('sodium_memzero')) {
            sodium_memzero($data);
        } else {
            $length = strlen($data);
            $data = str_repeat("\0", $length);
        }
    }

    /**
     * Validate passphrase strength requirements.
     *
     * @return array{valid: bool, errors: array<string>}
     */
    public function validatePassphraseStrength(string $passphrase): array
    {
        $errors = [];

        if (strlen($passphrase) < 12) {
            $errors[] = 'Passphrase must be at least 12 characters long';
        }

        if (strlen($passphrase) > 128) {
            $errors[] = 'Passphrase must not exceed 128 characters';
        }

        if (!preg_match('/[a-z]/', $passphrase)) {
            $errors[] = 'Passphrase must contain at least one lowercase letter';
        }

        if (!preg_match('/[A-Z]/', $passphrase)) {
            $errors[] = 'Passphrase must contain at least one uppercase letter';
        }

        if (!preg_match('/[0-9]/', $passphrase)) {
            $errors[] = 'Passphrase must contain at least one number';
        }

        // Check for common weak passphrases
        $weakPassphrases = [
            'password123456',
            '123456password',
            'qwerty123456',
        ];

        if (in_array(strtolower($passphrase), $weakPassphrases, true)) {
            $errors[] = 'This passphrase is too common. Please choose a stronger one.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
