<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Zero-Knowledge Encryption Key Storage
 *
 * This table stores the encrypted Data Encryption Keys (DEKs) for each team.
 * The DEKs are wrapped (encrypted) using the team's Master Encryption Key (MEK),
 * which is derived from the user's passphrase and NEVER stored.
 *
 * Security Properties:
 * - wrapped_dek: Encrypted using AES-256-GCM with the MEK (never stored in plaintext)
 * - key_salt: Used for Argon2id key derivation (safe to store)
 * - verification_hash: Allows checking if correct passphrase was entered
 * - key_version: Supports key rotation without data loss
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_encryption_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();

            // The Data Encryption Key wrapped (encrypted) with the user's Master Key
            // This is safe to store because it's encrypted with a key we don't have
            $table->text('wrapped_dek');

            // Salt for Argon2id key derivation (32 bytes, base64 encoded)
            // Safe to store - useless without the passphrase
            $table->string('key_salt', 64);

            // Hash to verify correct passphrase entry
            // Derived separately from the encryption key using different salt
            $table->string('verification_hash', 128);
            $table->string('verification_salt', 64);

            // IV/Nonce used for wrapping the DEK (12 bytes for AES-GCM, base64)
            $table->string('wrap_iv', 24);

            // Authentication tag from AES-GCM (16 bytes, base64)
            $table->string('wrap_auth_tag', 32);

            // Key version for rotation support
            $table->unsignedInteger('key_version')->default(1);

            // Timestamps for audit trail
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('rotated_at')->nullable();
            $table->timestamps();

            $table->unique('team_id');
            $table->index('key_version');
        });

        // Store encrypted session tokens for background job processing
        // These are short-lived tokens that allow scheduled jobs to decrypt data
        Schema::create('encryption_session_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // The MEK encrypted with a server-side session key
            // This enables background processing while maintaining security
            $table->text('encrypted_mek');

            // Session-specific encryption details
            $table->string('session_iv', 24);
            $table->string('session_auth_tag', 32);

            // Short expiration (max 1 hour for background jobs)
            $table->timestamp('expires_at');

            // Purpose: 'sync', 'export', 'report'
            $table->string('purpose', 50);

            // Unique token identifier for invalidation
            $table->string('token_id', 64)->unique();

            $table->timestamps();

            $table->index(['team_id', 'expires_at']);
            $table->index('token_id');
        });
    }
};
