# CashDash - Project Summary for AI Assistants

## What is CashDash?
CashDash is a **Swedish SaaS** that provides AI-driven cash flow analysis for small businesses using **Fortnox** (Sweden's most popular accounting software). Users connect their Fortnox account and get:
- **Runway calculation** - how many days until money runs out
- **12-month cash flow forecast**
- **AI-powered financial insights** (GPT-5.2)
- **Customer payment pattern analysis**

**Live URL:** https://cashdash.se
**Server:** forge@78.141.195.41 (Laravel Forge, PHP 8.4)

## Tech Stack
- **Backend:** Laravel 11, PHP 8.4
- **Frontend:** Livewire, Alpine.js, Tailwind CSS
- **Database:** MySQL (never SQLite)
- **Charts:** ApexCharts
- **AI:** OpenAI GPT-5.2 for insights
- **Auth:** Laravel Jetstream with Teams
- **Encryption:** Zero-knowledge AES-256

## Key Architecture Decisions

### Zero-Knowledge Encryption
- Users create a passphrase that encrypts all their Fortnox data
- Server NEVER has access to decrypted data
- **Encryption sessions** last 60 minutes after unlock
- Data sync only works when user has active session
- **IMPORTANT:** Scheduled background jobs CANNOT access encrypted data

### Fortnox Integration
- OAuth connection via Fortnox API
- Syncs: invoices, orders, supplier invoices, accounts
- Data encrypted with user's DEK (Data Encryption Key)
- Sync triggers on session unlock, not on schedule

## Important Files

### Controllers
- `app/Http/Controllers/EncryptionController.php` - passphrase setup, unlock, session management
- `app/Http/Controllers/FortnoxController.php` - OAuth flow, disconnect
- `app/Http/Controllers/BillingController.php` - billing page (Stripe not configured yet)

### Livewire
- `app/Livewire/Dashboard.php` - main dashboard with charts, insights, metrics

### Services
- `app/Services/AI/InsightsGenerator.php` - GPT-5.2 powered Swedish CFO assistant
- `app/Services/CashFlow/CashFlowCalculator.php` - runway, forecast calculations
- `app/Services/Encryption/EncryptionService.php` - AES-256 encryption

### Jobs
- `app/Jobs/SyncFortnoxData.php` - syncs data from Fortnox (requires encryption session)
- `app/Jobs/SyncFortnoxDataEncrypted.php` - encrypted version of sync

### Views
- `resources/views/landing.blade.php` - public landing page (heavy SEO)
- `resources/views/livewire/dashboard.blade.php` - main dashboard
- `resources/views/encryption/unlock.blade.php` - passphrase unlock screen

## Current Status (Dec 2024)

### Pricing
- **FREE** through January & February 2026 (launch period)
- 149 kr/month after that
- Stripe NOT configured yet - billing page just shows free period

### What Works
- Fortnox OAuth connection
- Zero-knowledge encryption setup & unlock
- Dashboard with runway, forecast, charts
- AI insights generation (GPT-5.2)
- Payment patterns analysis
- Session timer in navigation

### Known Limitations
- No scheduled background sync (encryption prevents it)
- Manual refresh only when session active
- Single user per team currently tested

## Common Issues & Fixes

### "Data disappeared / all zeros"
Bad snapshot created without encryption session. Fix:
```bash
ssh forge@78.141.195.41 "cd /home/forge/cashdash.se/current && php8.4 artisan tinker --execute=\"
\\\$team = App\\\\Models\\\\Team::find(1);
\\\$bad = \\\$team->cashSnapshots()->where('runway_days', 0)->where('cash_balance', 0)->first();
if (\\\$bad) \\\$bad->delete();
\""
```

### PHP version on server
Server default is PHP 8.3, but app requires 8.4:
```bash
ssh forge@78.141.195.41 "cd /home/forge/cashdash.se/current && php8.4 artisan ..."
```

### AI insights not working
Check OpenAI config:
```bash
ssh forge@78.141.195.41 "cd /home/forge/cashdash.se/current && php8.4 artisan tinker --execute=\"
echo config('openai.api_key') ? 'Key set' : 'No key';
echo config('openai.model');
\""
```

## Environment Variables (Server)
```
OPENAI_API_KEY=sk-proj-...
OPENAI_MODEL=gpt-5.2
OPENAI_ORGANIZATION=org-...
ANTHROPIC_API_KEY=sk-ant-api03-...
FORTNOX_CLIENT_ID=...
FORTNOX_CLIENT_SECRET=...
```

## Git Workflow
- **NEVER** commit without user saying "GITTA"
- Push directly to main (auto-deploys via Forge)
- Always run `php8.4` on server, not `php`

## Swedish Context
- All user-facing text is in Swedish
- Currency: SEK (kr)
- Tax dates: 12th of month (moms, arbetsgivaravgifter)
- AI insights reference Swedish business calendar

## Owner
Andreas Kviby - andreas@stafegroup.com
Company: Stafe Development AB
