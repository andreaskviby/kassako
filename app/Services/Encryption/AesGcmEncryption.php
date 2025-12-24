<?php

namespace App\Services\Encryption;

use RuntimeException;

/**
 * AES-256-GCM Encryption Service
 *
 * Provides authenticated encryption using AES-256 in Galois/Counter Mode (GCM).
 *
 * Why AES-256-GCM?
 * - Authenticated encryption: Detects tampering automatically
 * - Widely audited and trusted
 * - Hardware acceleration on modern CPUs (AES-NI)
 * - NIST recommended for sensitive data
 *
 * Security Properties:
 * - Confidentiality: Data is encrypted
 * - Integrity: Tampering is detected via authentication tag
 * - Authenticity: Proves data originated from someone with the key
 */
class AesGcmEncryption
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LENGTH = 12;      // 96 bits, recommended for GCM
    private const TAG_LENGTH = 16;     // 128 bits authentication tag
    private const KEY_LENGTH = 32;     // 256 bits

    /**
     * Encrypt data using AES-256-GCM.
     *
     * @param string $plaintext Data to encrypt
     * @param string $key Binary encryption key (32 bytes)
     * @param string $additionalData Optional AAD for authentication
     * @return array{ciphertext: string, iv: string, tag: string}
     */
    public function encrypt(string $plaintext, string $key, string $additionalData = ''): array
    {
        $this->validateKey($key);

        // Generate a unique IV for each encryption
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $additionalData,
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        return [
            'ciphertext' => base64_encode($ciphertext),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
        ];
    }

    /**
     * Decrypt data using AES-256-GCM.
     *
     * @param string $ciphertext Base64-encoded ciphertext
     * @param string $key Binary encryption key (32 bytes)
     * @param string $iv Base64-encoded IV
     * @param string $tag Base64-encoded authentication tag
     * @param string $additionalData Optional AAD for authentication
     * @return string Decrypted plaintext
     */
    public function decrypt(
        string $ciphertext,
        string $key,
        string $iv,
        string $tag,
        string $additionalData = ''
    ): string {
        $this->validateKey($key);

        $ciphertextBinary = base64_decode($ciphertext);
        $ivBinary = base64_decode($iv);
        $tagBinary = base64_decode($tag);

        if (strlen($ivBinary) !== self::IV_LENGTH) {
            throw new RuntimeException('Invalid IV length');
        }

        if (strlen($tagBinary) !== self::TAG_LENGTH) {
            throw new RuntimeException('Invalid authentication tag length');
        }

        $plaintext = openssl_decrypt(
            $ciphertextBinary,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $ivBinary,
            $tagBinary,
            $additionalData
        );

        if ($plaintext === false) {
            throw new RuntimeException('Decryption failed: Authentication failed or data corrupted');
        }

        return $plaintext;
    }

    /**
     * Wrap (encrypt) a DEK using a MEK.
     *
     * @param string $dek Binary DEK to wrap
     * @param string $mek Binary MEK (wrapping key)
     * @return array{wrapped_dek: string, iv: string, tag: string}
     */
    public function wrapKey(string $dek, string $mek): array
    {
        // Use team_id as additional authenticated data for domain separation
        $result = $this->encrypt($dek, $mek, 'key_wrap');

        return [
            'wrapped_dek' => $result['ciphertext'],
            'iv' => $result['iv'],
            'tag' => $result['tag'],
        ];
    }

    /**
     * Unwrap (decrypt) a DEK using a MEK.
     *
     * @param string $wrappedDek Base64-encoded wrapped DEK
     * @param string $mek Binary MEK (wrapping key)
     * @param string $iv Base64-encoded IV
     * @param string $tag Base64-encoded authentication tag
     * @return string Binary DEK
     */
    public function unwrapKey(string $wrappedDek, string $mek, string $iv, string $tag): string
    {
        return $this->decrypt($wrappedDek, $mek, $iv, $tag, 'key_wrap');
    }

    /**
     * Encrypt structured data as JSON.
     *
     * @param array<string, mixed> $data
     * @param string $key Binary encryption key
     * @param int $teamId For additional authentication
     * @return array{ciphertext: string, iv: string, tag: string}
     */
    public function encryptData(array $data, string $key, int $teamId): array
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR);

        return $this->encrypt($json, $key, "team:{$teamId}");
    }

    /**
     * Decrypt structured data from JSON.
     *
     * @param string $ciphertext
     * @param string $key Binary encryption key
     * @param string $iv
     * @param string $tag
     * @param int $teamId For additional authentication
     * @return array<string, mixed>
     */
    public function decryptData(
        string $ciphertext,
        string $key,
        string $iv,
        string $tag,
        int $teamId
    ): array {
        $json = $this->decrypt($ciphertext, $key, $iv, $tag, "team:{$teamId}");

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Validate the encryption key.
     */
    private function validateKey(string $key): void
    {
        if (strlen($key) !== self::KEY_LENGTH) {
            throw new RuntimeException(
                "Invalid key length: expected " . self::KEY_LENGTH .
                " bytes, got " . strlen($key)
            );
        }
    }

    /**
     * Generate a new random encryption key.
     */
    public function generateKey(): string
    {
        return random_bytes(self::KEY_LENGTH);
    }
}
