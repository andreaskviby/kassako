# Fortnox Integration

## API Credentials

| Nyckel | Värde |
|--------|-------|
| **Client ID** | `c6KknCOKVNBh` |
| **Client Secret** | `9mK3W3TKBcJdv4Ac5zhAhMxVJecvV2Fh` |
| **Redirect URI** | `http://kassako.test/fortnox/callback` |

## Scopes

```
companyinformation invoice customer order supplier article bookkeeping
```

## OAuth Flow

1. Användare klickar "Koppla Fortnox" i CashDash
2. Redirect till Fortnox OAuth
3. Användare godkänner behörigheter
4. Callback till `/fortnox/callback` med auth code
5. Byt auth code mot access/refresh tokens

## Developer Portal

- URL: https://developer.fortnox.se/
- App Dashboard: https://developer.fortnox.se/apps

## Dokumentation

- API Docs: https://developer.fortnox.se/documentation/
- OAuth Guide: https://developer.fortnox.se/general/authentication/
