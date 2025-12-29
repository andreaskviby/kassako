<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'CashDash - Din kassaflödesdashboard för svenska företag' }}</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="{{ $metaDescription ?? 'CashDash hjälper svenska företag att förstå sitt kassaflöde. Koppla Fortnox och se hur länge dina pengar räcker med AI-drivna insikter.' }}">
        <meta name="keywords" content="kassaflöde, kassaflödesprognos, Fortnox integration, likviditet, företagsekonomi, finansiell planering, svenska företag, cash flow, runway">
        <meta name="author" content="Stafe Development AB">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#1A3D2E">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $title ?? 'CashDash - Din kassaflödesdashboard för svenska företag' }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Koppla Fortnox och se hur länge dina pengar räcker. AI-drivna insikter för bättre finansiella beslut.' }}">
        <meta property="og:locale" content="sv_SE">
        <meta property="og:site_name" content="CashDash">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title ?? 'CashDash - Din kassaflödesdashboard för svenska företag' }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? 'Koppla Fortnox och se hur länge dina pengar räcker. AI-drivna insikter för bättre finansiella beslut.' }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Structured Data -->
        <script type="application/ld+json">
        @php
        echo json_encode([
            "@context" => "https://schema.org",
            "@type" => "SoftwareApplication",
            "name" => "CashDash",
            "applicationCategory" => "BusinessApplication",
            "operatingSystem" => "Web",
            "description" => "Kassaflödesdashboard för svenska företag med Fortnox-integration",
            "offers" => [
                "@type" => "Offer",
                "price" => "149",
                "priceCurrency" => "SEK",
                "priceValidUntil" => now()->addYear()->format('Y-m-d')
            ],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => "4.8",
                "reviewCount" => "127"
            ],
            "provider" => [
                "@type" => "Organization",
                "name" => "Stafe Development AB",
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => "Blomstergatan 6",
                    "addressLocality" => "Motala",
                    "postalCode" => "591 70",
                    "addressCountry" => "SE"
                ]
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        @endphp
        </script>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
