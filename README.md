# CashDash

**Din kassaflodesdashboard for svenska foretag**

CashDash ar en SaaS-tjanst som ger svenska foretagare full kontroll over sitt kassaflode genom att integrera med Fortnox. Se hur lange dina pengar racker med AI-drivna insikter och prognoser.

## Funktioner

- **Kassaflodesforlopp** - Se hur manga dagar dina pengar racker
- **Fortnox-integration** - Automatisk synkronisering av fakturor och betalningar
- **12-manaders prognos** - AI-drivna kassaflodesprognoser
- **Betalningsmonster** - Analysera kunders betalningsbeteende
- **AI-insikter** - Automatiska varningar och rekommendationer
- **Zero-Knowledge Encryption** - Din data krypteras med din egen nyckel

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** TALL Stack (Tailwind CSS, Alpine.js, Livewire)
- **Database:** MySQL
- **Betalningar:** Stripe
- **Integration:** Fortnox API

## Installation

### Krav

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL 8.0+
- Laravel Herd (rekommenderat for lokal utveckling)

### Steg

1. Klona projektet
```bash
git clone https://github.com/your-repo/cashdash.git
cd cashdash
```

2. Installera PHP-beroenden
```bash
composer install
```

3. Installera Node-beroenden
```bash
npm install
```

4. Kopiera miljofilen
```bash
cp .env.example .env
```

5. Generera applikationsnyckel
```bash
php artisan key:generate
```

6. Konfigurera databas i `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cashdash
DB_USERNAME=root
DB_PASSWORD=
```

7. Kor migrationer
```bash
php artisan migrate
```

8. Bygg frontend-assets
```bash
npm run build
```

9. Starta utvecklingsservern
```bash
php artisan serve
# eller med Herd - besoek https://cashdash.test
```

## Utveckling

### Starta utvecklingsmiljon

```bash
npm run dev
```

### Kora tester

```bash
php artisan test
```

### Kora Fortnox-synkronisering

```bash
php artisan fortnox:sync
```

## Konfiguration

### Fortnox API

Lagg till dina Fortnox API-nycklar i `.env`:

```env
FORTNOX_CLIENT_ID=your_client_id
FORTNOX_CLIENT_SECRET=your_client_secret
```

### Stripe

Konfigurera Stripe for betalningar:

```env
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

### E-post (Laravel Herd)

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=${APP_NAME}
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@cashdash.se"
MAIL_FROM_NAME="${APP_NAME}"
```

## Dokumentation

- [Encryption Architecture](/docs/ENCRYPTION_ARCHITECTURE.md)
- [Landing Page Design Spec](/docs/LANDING_PAGE_DESIGN_SPEC.md)

## Licens

Proprietar - Stafe Group AB

## Kontakt

- **Webb:** https://cashdash.se
- **Foretag:** Stafe Group AB
- **Adress:** Blomstergatan 6, 591 70 Motala
