<?php

return [
    'client_id' => env('FORTNOX_CLIENT_ID'),
    'client_secret' => env('FORTNOX_CLIENT_SECRET'),
    'redirect_uri' => env('FORTNOX_REDIRECT_URI'),
    'scopes' => env('FORTNOX_SCOPES', 'companyinformation'),
    'webhook_secret' => env('FORTNOX_WEBHOOK_SECRET'),

    // Fortnox uses the same endpoints for production and test environments
    // Test environments are created as "testmiljöer" in the Developer Portal
    'api_base_url' => 'https://api.fortnox.se/3/',
    'auth_url' => 'https://apps.fortnox.se/oauth-v1/auth',
    'token_url' => 'https://apps.fortnox.se/oauth-v1/token',
];
