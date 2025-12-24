# Kassako Encryption Implementation Summary

## Files Created

### Database Migrations

1. **`/database/migrations/2025_12_23_210000_create_team_encryption_keys_table.php`**
   - Creates `team_encryption_keys` table for storing wrapped DEKs
   - Creates `encryption_session_tokens` table for background job support

2. **`/database/migrations/2025_12_23_210001_add_encryption_columns_to_data_tables.php`**
   - Adds encryption columns to all sensitive data tables:
     - `encrypted_data` (TEXT) - The encrypted JSON blob
     - `encryption_iv` (VARCHAR) - Initialization vector
     - `encryption_auth_tag` (VARCHAR) - Authentication tag
     - `encryption_version` (INT) - Key version used
     - `is_encrypted` (BOOLEAN) - Encryption status flag

### Core Encryption Services

3. **`/app/Services/Encryption/KeyDerivationService.php`**
   - Argon2id key derivation from passphrases
   - Salt generation
   - Passphrase verification
   - Passphrase strength validation

4. **`/app/Services/Encryption/AesGcmEncryption.php`**
   - AES-256-GCM encryption/decryption
   - Key wrapping/unwrapping
   - JSON data encryption

5. **`/app/Services/Encryption/TeamEncryptionService.php`**
   - Main encryption orchestration
   - Key initialization
   - Session unlock/lock
   - Passphrase changes
   - Session token management
   - Key destruction

6. **`/app/Services/Encryption/DataEncryptorService.php`**
   - Bulk data encryption
   - Per-table encryption methods
   - Migration of existing data

### Models

7. **`/app/Models/TeamEncryptionKey.php`**
   - Eloquent model for encryption keys

8. **`/app/Models/EncryptionSessionToken.php`**
   - Eloquent model for session tokens

9. **`/app/Models/Team.php`** (Modified)
   - Added `encryptionKey()` relationship
   - Added `encryptionSessionTokens()` relationship
   - Added `hasEncryptionInitialized()` method
   - Added `isEncryptionUnlocked()` method

### Traits

10. **`/app/Traits/HasEncryptedData.php`**
    - Reusable trait for encrypted models
    - Automatic encryption on save
    - Decryption helpers

### Controllers

11. **`/app/Http/Controllers/EncryptionController.php`**
    - Encryption setup
    - Unlock/lock
    - Passphrase change
    - Session token creation
    - Status endpoint

### Middleware

12. **`/app/Http/Middleware/RequireEncryptionUnlocked.php`**
    - Route protection
    - Redirects to unlock page

### Event Listeners

13. **`/app/Listeners/HandleSubscriptionCancellation.php`**
    - Handles Stripe subscription events
    - Destroys encryption keys on cancellation

### Console Commands

14. **`/app/Console/Commands/CleanupExpiredEncryptionTokensCommand.php`**
    - Hourly cleanup of expired tokens

15. **`/app/Console/Commands/MigrateToEncryptionCommand.php`**
    - Migration status reporting
    - Helps track encryption adoption

### Service Provider

16. **`/app/Providers/EncryptionServiceProvider.php`**
    - Registers all encryption services
    - Registers event listeners
    - Registers middleware alias

### Views

17. **`/resources/views/encryption/setup.blade.php`**
    - Initial encryption setup page

18. **`/resources/views/encryption/unlock.blade.php`**
    - Encryption unlock page

### Routes (Modified)

19. **`/routes/web.php`**
    - Added encryption routes
    - Protected dashboard routes with `encryption.unlocked` middleware

20. **`/routes/console.php`**
    - Added hourly token cleanup schedule

### Configuration (Modified)

21. **`/bootstrap/providers.php`**
    - Added EncryptionServiceProvider

### Documentation

22. **`/docs/ENCRYPTION_ARCHITECTURE.md`**
    - Complete technical documentation
    - User-facing explanations
    - GDPR compliance notes

---

## Deployment Steps

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Clear Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Schedule Token Cleanup

Ensure your scheduler is running:

```bash
php artisan schedule:work
# or in production
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Notify Users

Send email to all existing teams informing them:
- New security feature available
- They need to set up an encryption passphrase
- Importance of saving the passphrase

### 5. Monitor Migration

```bash
php artisan encryption:migration-status --detailed
```

---

## Security Checklist

- [ ] APP_KEY is set and backed up securely
- [ ] Database backups are encrypted
- [ ] HTTPS is enforced
- [ ] Session lifetime is appropriate (30 min)
- [ ] Stripe webhooks are verified
- [ ] Logging does not contain sensitive data
- [ ] Error pages do not leak encryption details

---

## Testing Commands

```bash
# Check encryption status
php artisan encryption:migration-status

# Clean up expired tokens manually
php artisan encryption:cleanup-tokens

# Test in tinker
php artisan tinker
```

---

## API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | /encryption/setup | Show setup page |
| POST | /encryption/setup | Initialize encryption |
| GET | /encryption/unlock | Show unlock page |
| POST | /encryption/unlock | Unlock session |
| POST | /encryption/lock | Lock session |
| GET | /encryption/status | JSON status check |
| GET | /encryption/change-passphrase | Change passphrase form |
| POST | /encryption/change-passphrase | Update passphrase |
| POST | /encryption/session-token | Create background job token |

---

## Updating FortnoxController for Encrypted Tokens

The FortnoxController needs to be updated to work with encrypted tokens. Here's the pattern:

```php
// In FortnoxController, when saving tokens:
public function callback(Request $request)
{
    $team = $request->user()->currentTeam;
    $sessionId = session('encryption_session_id');

    // Get tokens from Fortnox OAuth
    $tokens = $this->exchangeCode($request->code);

    // The DataEncryptorService will encrypt these
    $connection = $team->fortnoxConnection()->updateOrCreate(
        ['team_id' => $team->id],
        [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            // ... other fields
        ]
    );

    // Encrypt the new connection
    $encryptor = app(DataEncryptorService::class);
    $dek = app(TeamEncryptionService::class)->getDek($team, $sessionId);
    $encryptor->encryptFortnoxConnection($connection, $dek, $team->encryptionKey->key_version);
}
```

---

## Next Steps

1. Update FortnoxController to use encrypted token storage
2. Update SyncFortnoxData job to use session tokens
3. Update Dashboard to decrypt data for display
4. Add "Lock" button to navigation
5. Create Swedish translations for all encryption UI
6. Add email notifications for:
   - Encryption setup reminders
   - Passphrase change confirmations
   - Subscription cancellation warnings
