# Kassako Zero-Knowledge Encryption Architecture

## For Swedish Business Owners: What This Means For You

### The Simple Explanation

When you use Kassako, your financial data from Fortnox is encrypted with a passphrase that only you know. This means:

1. **We Cannot See Your Data**: Even we (Kassako) cannot read your financial information
2. **Your Data is Protected**: If our database were ever compromised, your data would be cryptographically unreadable
3. **You Have Full Control**: Only you can unlock and view your data
4. **GDPR Compliant**: When you leave, deleting your key makes all data permanently inaccessible

### What You Need to Know

- **Remember Your Passphrase**: We cannot recover it for you
- **Use a Password Manager**: Store your passphrase safely (like Bitwarden, 1Password, or LastPass)
- **Session Timeout**: Your session locks after 30 minutes of inactivity for security

---

## Technical Architecture

### Encryption Hierarchy

```
User's Passphrase (never stored)
         |
         v
    [Argon2id KDF]
         |
         v
Master Encryption Key (MEK) - held in memory only
         |
         v
    [AES-256-GCM]
         |
         v
Data Encryption Key (DEK) - stored encrypted
         |
         v
    [AES-256-GCM]
         |
         v
Your Financial Data - stored encrypted
```

### Key Components

#### 1. Key Derivation (Argon2id)

We use Argon2id, the winner of the Password Hashing Competition, to derive encryption keys from your passphrase:

- **Memory-hard**: Resistant to GPU/ASIC attacks
- **Time-cost**: Multiple iterations for additional security
- **Salt**: Unique per team, prevents rainbow table attacks

Parameters:
- Memory: 64 MiB
- Iterations: 3
- Parallelism: 4
- Output: 256-bit key

#### 2. Data Encryption (AES-256-GCM)

All data is encrypted using AES-256 in Galois/Counter Mode:

- **Authenticated Encryption**: Detects tampering
- **256-bit Key**: Bank-grade security level
- **Unique IV per Record**: No two encryptions are the same
- **Hardware Acceleration**: Fast on modern CPUs

#### 3. Envelope Encryption

We use a two-layer key system:

1. **Master Encryption Key (MEK)**: Derived from your passphrase, never stored
2. **Data Encryption Key (DEK)**: Random key, stored encrypted with MEK

Benefits:
- Changing your passphrase doesn't require re-encrypting all data
- Key rotation is efficient
- Different data categories can have different DEKs

### What Gets Encrypted

| Table | Encrypted Fields | Queryable Fields |
|-------|------------------|------------------|
| fortnox_connections | access_token, refresh_token, company_name, org_number | team_id, is_active |
| cash_snapshots | All financial amounts, forecasts, insights | team_id, snapshot_date |
| fortnox_invoices | customer_name, customer_number, amounts | team_id, dates, status |
| fortnox_supplier_invoices | supplier_name, supplier_number, amounts | team_id, dates, status |
| fortnox_orders | customer_name, customer_number, amounts | team_id, dates, status |
| customer_payment_patterns | customer_name, revenue data | team_id |

### Security Properties

#### Zero-Knowledge Architecture

1. **Passphrase**: Never transmitted after initial key derivation
2. **MEK**: Never stored, only in memory during active session
3. **DEK**: Stored encrypted, useless without MEK
4. **Data**: Stored encrypted, useless without DEK

#### Defense in Depth

1. **Transport**: TLS 1.3 for all connections
2. **At Rest**: AES-256-GCM encryption
3. **Key Storage**: Encrypted with user-derived key
4. **Access Control**: Session-based with automatic timeout
5. **Key Deletion**: Subscription cancellation destroys keys

#### Forward Secrecy

Each encryption operation uses a unique IV, ensuring that:
- Same data encrypted twice produces different ciphertext
- Compromising one record doesn't help with others

### Session Management

#### Interactive Sessions

1. User enters passphrase
2. MEK derived and cached (encrypted) for 30 minutes
3. Session extended on activity
4. Automatic lock on timeout

#### Background Jobs (Fortnox Sync)

1. User creates session token before sync
2. Token contains encrypted MEK
3. Token expires after max 60 minutes
4. Automatic cleanup of expired tokens

### Subscription Cancellation

When a subscription ends:

1. All session tokens are immediately deleted
2. The team encryption key (DEK) is permanently deleted
3. All encrypted data becomes cryptographically inaccessible
4. This is irreversible by design (crypto-shredding)

This provides:
- GDPR Article 17 compliance (Right to Erasure)
- Complete data deletion guarantee
- Audit trail preservation (encrypted data can be retained)

---

## EU Regulatory Compliance

### GDPR (General Data Protection Regulation)

| Requirement | How We Comply |
|-------------|---------------|
| Article 5 - Data Minimization | Only sync necessary Fortnox data |
| Article 17 - Right to Erasure | Key deletion = complete data erasure |
| Article 25 - Privacy by Design | Zero-knowledge architecture |
| Article 32 - Security | AES-256-GCM, Argon2id |
| Article 33/34 - Breach Notification | Encrypted data = reduced impact |

### Swedish Requirements

- Data processing agreement with Fortnox
- Data stored within EU (Swedish/EU cloud infrastructure)
- Transparent privacy policy in Swedish

---

## Implementation Files

### Core Services

- `/app/Services/Encryption/KeyDerivationService.php` - Argon2id key derivation
- `/app/Services/Encryption/AesGcmEncryption.php` - AES-256-GCM operations
- `/app/Services/Encryption/TeamEncryptionService.php` - Main encryption orchestration
- `/app/Services/Encryption/DataEncryptorService.php` - Bulk data encryption

### Models

- `/app/Models/TeamEncryptionKey.php` - Stored encryption keys
- `/app/Models/EncryptionSessionToken.php` - Background job tokens

### Middleware

- `/app/Http/Middleware/RequireEncryptionUnlocked.php` - Route protection

### Migrations

- `2025_12_23_210000_create_team_encryption_keys_table.php`
- `2025_12_23_210001_add_encryption_columns_to_data_tables.php`

---

## Security Recommendations

### For Development

1. Never log passphrases or encryption keys
2. Clear sensitive data from memory after use
3. Use constant-time comparison for hashes
4. Regular security audits

### For Operations

1. Enable audit logging for key operations
2. Monitor for unusual decryption patterns
3. Regular token cleanup jobs
4. Backup encrypted data only (never keys)

### For Users

1. Use strong, unique passphrases (12+ characters)
2. Store passphrase in a password manager
3. Lock session when leaving computer
4. Report any suspicious activity

---

## Testing the Implementation

```bash
# Run migrations
php artisan migrate

# Test encryption initialization
php artisan tinker
> $team = Team::first();
> app(TeamEncryptionService::class)->initializeEncryption($team, 'TestPassphrase123!');

# Test unlock/lock cycle
> $sessionId = Str::uuid()->toString();
> app(TeamEncryptionService::class)->unlockEncryption($team, 'TestPassphrase123!', $sessionId);
> app(TeamEncryptionService::class)->isUnlocked($team, $sessionId); // true
> app(TeamEncryptionService::class)->lockEncryption($team, $sessionId);
> app(TeamEncryptionService::class)->isUnlocked($team, $sessionId); // false
```

---

## Audit Trail

All encryption operations are logged:

- Key initialization (timestamp, team_id)
- Unlock/lock events (timestamp, team_id, user_id)
- Session token creation (timestamp, team_id, purpose)
- Key destruction (timestamp, team_id, reason)

Logs do NOT contain:
- Passphrases
- Encryption keys
- Decrypted data
