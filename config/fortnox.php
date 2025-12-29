<?php

$sandbox = env('FORTNOX_SANDBOX', false);

return [
    'client_id' => env('FORTNOX_CLIENT_ID'),
    'client_secret' => env('FORTNOX_CLIENT_SECRET'),
    'redirect_uri' => env('FORTNOX_REDIRECT_URI'),
    'scopes' => env('FORTNOX_SCOPES', 'companyinformation invoice supplierinvoice order customer bookkeeping'),
    'webhook_secret' => env('FORTNOX_WEBHOOK_SECRET'),
    'sandbox' => $sandbox,

    // URLs switch based on environment
    'api_base_url' => $sandbox
        ? 'https://api.sandbox.fortnox.se/3/'
        : 'https://api.fortnox.se/3/',
    'auth_url' => $sandbox
        ? 'https://apps.sandbox.fortnox.se/oauth-v1/auth'
        : 'https://apps.fortnox.se/oauth-v1/auth',
    'token_url' => $sandbox
        ? 'https://apps.sandbox.fortnox.se/oauth-v1/token'
        : 'https://apps.fortnox.se/oauth-v1/token',
];
