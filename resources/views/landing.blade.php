<!DOCTYPE html>
<html lang="sv" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>CashDash - Kassaflödesdashboard för Svenska Företag | Fortnox Integration</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="CashDash - Kassaflödesdashboard för Svenska Företag | Fortnox Integration">
    <meta name="description" content="Se hur länge dina pengar räcker med CashDash. Koppla Fortnox och få AI-drivna kassaflödesprognoser, realtidsinsikter och 12-månaders prognoser. Prova gratis i 14 dagar.">
    <meta name="keywords" content="kassaflöde, kassaflödesprognos, Fortnox, likviditetsprognos, cash runway, företagsekonomi, finansiell planering, småföretag Sverige, bokföring, AI prognos, kassaposition">
    <meta name="author" content="Stafe Development AB">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#1A3D2E">
    <meta name="language" content="Swedish">
    <meta name="geo.region" content="SE">
    <meta name="geo.placename" content="Motala">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://cashdash.se">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cashdash.se">
    <meta property="og:title" content="CashDash - Se hur länge dina pengar räcker">
    <meta property="og:description" content="Koppla Fortnox och få AI-drivna kassaflödesprognoser. Realtidsinsikter för bättre finansiella beslut. 14 dagars gratis provperiod.">
    <meta property="og:locale" content="sv_SE">
    <meta property="og:site_name" content="CashDash">
    <meta property="og:image" content="https://cashdash.se/images/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://cashdash.se">
    <meta name="twitter:title" content="CashDash - Kassaflödesdashboard för Svenska Företag">
    <meta name="twitter:description" content="Se hur länge dina pengar räcker. Koppla Fortnox och få AI-drivna kassaflödesprognoser.">
    <meta name="twitter:image" content="https://cashdash.se/images/og-image.png">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA Safe Areas -->
    <style>
        :root {
            --sat: env(safe-area-inset-top);
            --sab: env(safe-area-inset-bottom);
            --sal: env(safe-area-inset-left);
            --sar: env(safe-area-inset-right);
        }
    </style>

    <!-- Structured Data -->
    @php
    $structuredData = [
        // Organization
        [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "CashDash",
            "legalName" => "Stafe Development AB",
            "url" => "https://cashdash.se",
            "logo" => "https://cashdash.se/images/logo.svg",
            "description" => "Kassaflödesdashboard för svenska företag med Fortnox-integration",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "Blomstergatan 6",
                "addressLocality" => "Motala",
                "postalCode" => "591 70",
                "addressCountry" => "SE"
            ],
            "sameAs" => [
                "https://linkedin.com/company/cashdash",
                "https://twitter.com/cashdash"
            ]
        ],
        // SoftwareApplication
        [
            "@context" => "https://schema.org",
            "@type" => "SoftwareApplication",
            "name" => "CashDash",
            "applicationCategory" => "BusinessApplication",
            "operatingSystem" => "Web",
            "description" => "AI-driven kassaflödesdashboard med Fortnox-integration för svenska företag.",
            "offers" => [
                "@type" => "Offer",
                "price" => "149",
                "priceCurrency" => "SEK",
                "priceValidUntil" => now()->addYear()->format('Y-m-d'),
                "availability" => "https://schema.org/InStock"
            ],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => "4.9",
                "ratingCount" => "127",
                "bestRating" => "5",
                "worstRating" => "1"
            ],
            "featureList" => [
                "Fortnox-integration",
                "12-månaders kassaflödesprognos",
                "AI-drivna insikter",
                "Realtidsuppdateringar",
                "Betalningsmönsteranalys",
                "Bankgradig kryptering"
            ]
        ],
        // FAQPage
        [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => [
                [
                    "@type" => "Question",
                    "name" => "Hur säkerhetsskyddas min data?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Din data krypteras med din egen lösenfras som endast du har tillgång till. Vi använder AES-256-kryptering och Zero-Knowledge-arkitektur. All data lagras i svenska datacenter."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "Hur fungerar Fortnox-integrationen?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Du kopplar ditt Fortnox-konto med ett klick via Fortnox egna inloggning. Vi hämtar sedan automatiskt relevanta data som fakturor, betalningar och kontosaldon."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "Vad kostar CashDash?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "CashDash kostar 149 kr per månad med 14 dagars gratis provperiod. Inget kreditkort krävs och du kan avsluta när som helst."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "Hur ofta uppdateras min data?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Data synkroniseras automatiskt varje timme från Fortnox. Du kan också manuellt starta en synkronisering när som helst."
                    ]
                ]
            ]
        ],
        // BreadcrumbList
        [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Hem",
                    "item" => "https://cashdash.se"
                ]
            ]
        ]
    ];
    @endphp
    @foreach($structuredData as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
</head>
<body class="bg-cashdash-cream text-cashdash-text antialiased">

    <!-- ========================================
         1. NAVIGATION (Sticky, Minimal)
         ======================================== -->
    <nav class="nav-sticky" id="navbar" role="navigation" aria-label="Huvudnavigation">
        <div class="container-landing">
            <div class="flex items-center justify-between h-16 md:h-18">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2" aria-label="CashDash hem">
                    <img src="/images/logo.svg" alt="CashDash" class="w-10 h-10">
                    <span class="font-display font-bold text-xl text-cashdash-forest">Cash<span class="text-[#C4A962]">Dash</span></span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#hur-det-fungerar" class="nav-link">Hur det fungerar</a>
                    <a href="#funktioner" class="nav-link">Funktioner</a>
                    <a href="#priser" class="nav-link">Priser</a>
                    <a href="#faq" class="nav-link">FAQ</a>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex nav-link">Logga in</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm">
                            Prova gratis
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button
                        type="button"
                        class="md:hidden p-2 rounded-lg hover:bg-forest-50 transition-colors"
                        aria-label="Öppna meny"
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                    >
                        <svg class="w-6 h-6 text-cashdash-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col gap-2">
                    <a href="#hur-det-fungerar" class="py-3 px-4 rounded-lg hover:bg-forest-50 transition-colors">Hur det fungerar</a>
                    <a href="#funktioner" class="py-3 px-4 rounded-lg hover:bg-forest-50 transition-colors">Funktioner</a>
                    <a href="#priser" class="py-3 px-4 rounded-lg hover:bg-forest-50 transition-colors">Priser</a>
                    <a href="#faq" class="py-3 px-4 rounded-lg hover:bg-forest-50 transition-colors">FAQ</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- ========================================
             2. HERO SECTION with Animated Counter
             ======================================== -->
        <section class="section pt-32 md:pt-40 lg:pt-48 pb-16 md:pb-24" aria-labelledby="hero-heading">
            <div class="container-landing">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Fortnox Integration Badge -->
                    <div class="fortnox-badge mb-8 animate-fade-in">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        <span>Integrerar med Fortnox</span>
                    </div>

                    <!-- Hero Headline -->
                    <h1 id="hero-heading" class="font-display text-hero-mobile md:text-hero text-cashdash-text mb-6 animate-fade-in-up">
                        Se hur länge dina
                        <span class="text-cashdash-forest">pengar räcker</span>
                    </h1>

                    <!-- Dramatic Runway Counter -->
                    <div class="my-12 md:my-16 animate-fade-in-up animation-delay-200">
                        <div class="runway-counter-wrapper">
                            <div class="runway-counter-glow" aria-hidden="true"></div>
                            <div class="flex items-baseline justify-center gap-4">
                                <span
                                    class="runway-counter"
                                    id="runway-days"
                                    data-target="87"
                                    aria-live="polite"
                                    aria-label="87 dagars kassaförlopp"
                                >0</span>
                                <span class="font-display text-4xl md:text-6xl text-cashdash-gold font-bold">dagar</span>
                            </div>
                        </div>
                        <p class="runway-label mt-4">Kassaförlopp</p>
                    </div>

                    <!-- Hero Description -->
                    <p class="text-lg md:text-xl text-cashdash-muted max-w-2xl mx-auto mb-10 animate-fade-in-up animation-delay-300">
                        CashDash kopplar till ditt Fortnox och ger dig realtidsinsikter om ditt kassaflöde.
                        Fatta smartare finansiella beslut med AI-drivna prognoser.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up animation-delay-400">
                        <a href="{{ route('register') }}" class="btn-gold w-full sm:w-auto text-base px-8">
                            Starta 14 dagars gratis provperiod
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#hur-det-fungerar" class="btn-secondary w-full sm:w-auto text-base">
                            Se hur det fungerar
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-sm text-cashdash-muted animate-fade-in animation-delay-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Inget kreditkort kravs</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Uppsägning när som helst</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Säkerhetskrypterad data</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             3. HOW IT WORKS - 3 Steps
             ======================================== -->
        <section id="hur-det-fungerar" class="section bg-white" aria-labelledby="how-it-works-heading">
            <div class="container-landing">
                <div class="section-header">
                    <h2 id="how-it-works-heading" class="section-title">Hur det fungerar</h2>
                    <p class="section-subtitle">Tre enkla steg till bättre kassaflödesinsikter</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 md:gap-4 max-w-5xl mx-auto">
                    <!-- Step 1 -->
                    <div class="step-card">
                        <div class="step-connector" aria-hidden="true"></div>
                        <div class="step-number" aria-hidden="true">1</div>
                        <h3 class="font-display font-semibold text-xl text-cashdash-text mb-2">Koppla Fortnox</h3>
                        <p class="text-cashdash-muted">
                            Anslut ditt Fortnox-konto med ett klick. Säkert och krypterat via OAuth.
                        </p>
                        <div class="mt-4">
                            <svg class="w-16 h-16 mx-auto text-cashdash-forest opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-card">
                        <div class="step-connector" aria-hidden="true"></div>
                        <div class="step-number" aria-hidden="true">2</div>
                        <h3 class="font-display font-semibold text-xl text-cashdash-text mb-2">Synka data</h3>
                        <p class="text-cashdash-muted">
                            Vi hämtar automatiskt fakturor, betalningar och saldon. Uppdateras varje timme.
                        </p>
                        <div class="mt-4">
                            <svg class="w-16 h-16 mx-auto text-cashdash-forest opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-card">
                        <div class="step-number" aria-hidden="true">3</div>
                        <h3 class="font-display font-semibold text-xl text-cashdash-text mb-2">Se förlopp</h3>
                        <p class="text-cashdash-muted">
                            Få omedelbar insikt i hur många dagar dina pengar räcker baserat på verklig data.
                        </p>
                        <div class="mt-4">
                            <svg class="w-16 h-16 mx-auto text-cashdash-forest opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             4. FEATURES GRID
             ======================================== -->
        <section id="funktioner" class="section" aria-labelledby="features-heading">
            <div class="container-landing">
                <div class="section-header">
                    <h2 id="features-heading" class="section-title">Kraftfulla funktioner</h2>
                    <p class="section-subtitle">Allt du behöver för att ha full koll på ditt kassaflöde</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    <!-- Feature 1: Cash Position -->
                    <article class="feature-card group">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="font-display font-semibold text-lg text-cashdash-text mb-2">Kassaposition</h3>
                        <p class="text-cashdash-muted text-sm">
                            Se ditt aktuella saldo och tillgängliga medel i realtid. Alltid uppdaterat.
                        </p>
                    </article>

                    <!-- Feature 2: 12-Month Forecast -->
                    <article class="feature-card group">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-display font-semibold text-lg text-cashdash-text mb-2">12-månaders prognos</h3>
                        <p class="text-cashdash-muted text-sm">
                            Planera framåt med detaljerade prognoser baserade på historisk data och trender.
                        </p>
                    </article>

                    <!-- Feature 3: AI Insights -->
                    <article class="feature-card group">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="font-display font-semibold text-lg text-cashdash-text mb-2">AI-insikter</h3>
                        <p class="text-cashdash-muted text-sm">
                            Intelligent analys som identifierar risker och möjligheter i ditt kassaflöde.
                        </p>
                    </article>

                    <!-- Feature 4: Payment Patterns -->
                    <article class="feature-card group">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-display font-semibold text-lg text-cashdash-text mb-2">Betalningsmönster</h3>
                        <p class="text-cashdash-muted text-sm">
                            Förstå dina kunders betalningsbeteende och förutsäg framtida inflöden.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ========================================
             5. DASHBOARD PREVIEW - MIND-BLOWING ANIMATED VERSION
             ======================================== -->
        <section class="section bg-gradient-to-b from-white via-forest-50/30 to-white overflow-hidden" aria-labelledby="preview-heading" id="dashboard-preview-section">
            <div class="container-landing">
                <div class="section-header">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-cashdash-forest/10 rounded-full text-cashdash-forest text-sm font-medium mb-4">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cashdash-success opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-cashdash-success"></span>
                        </span>
                        Realtidsdata från Fortnox
                    </span>
                    <h2 id="preview-heading" class="section-title">Intuitivt gränssnitt</h2>
                    <p class="section-subtitle">Designat för att ge dig snabb översikt utan komplexitet</p>
                </div>

                <!-- Floating Elements Background -->
                <div class="relative">
                    <div class="absolute -top-20 -left-20 w-72 h-72 bg-cashdash-gold/10 rounded-full blur-3xl animate-pulse-soft"></div>
                    <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-cashdash-forest/10 rounded-full blur-3xl animate-pulse-soft" style="animation-delay: 1s;"></div>

                    <div class="max-w-6xl mx-auto relative">
                        <!-- Browser Window Frame -->
                        <div class="dashboard-preview-wrapper rounded-2xl shadow-2xl overflow-hidden border border-forest-200/50 bg-white/80 backdrop-blur-sm" id="dashboard-mockup">
                            <!-- macOS Style Header -->
                            <div class="bg-gradient-to-r from-cashdash-forest to-forest-600 px-4 py-3 flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-400 hover:bg-red-500 transition-colors cursor-pointer"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400 hover:bg-yellow-500 transition-colors cursor-pointer"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400 hover:bg-green-500 transition-colors cursor-pointer"></div>
                                </div>
                                <div class="flex-1 flex justify-center">
                                    <div class="flex items-center gap-2 bg-white/10 rounded-lg px-4 py-1.5">
                                        <svg class="w-4 h-4 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="text-white/80 text-sm font-medium">app.cashdash.se</span>
                                    </div>
                                </div>
                                <div class="w-20"></div>
                            </div>

                            <!-- Dashboard Content -->
                            <div class="bg-gradient-to-br from-cashdash-cream to-forest-50/50 p-4 md:p-6 lg:p-8">
                                <!-- Top Stats Row -->
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
                                    <!-- Runway Days - Hero Stat -->
                                    <div class="col-span-2 lg:col-span-1 bg-gradient-to-br from-cashdash-forest to-forest-600 rounded-2xl p-5 md:p-6 text-white relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                                        <div class="relative z-10">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-5 h-5 text-cashdash-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                                <span class="text-white/80 text-sm font-medium">Kassaförlopp</span>
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                                <span class="font-display text-4xl md:text-5xl font-bold dashboard-counter" data-target="87">0</span>
                                                <span class="text-cashdash-gold font-semibold text-lg">dagar</span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-3 text-green-300 text-sm">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span>+12 dagar sedan förra veckan</span>
                                            </div>
                                        </div>
                                        <!-- Mini Sparkline -->
                                        <div id="runway-sparkline" class="absolute bottom-2 right-2 w-20 h-10 opacity-50"></div>
                                    </div>

                                    <!-- Current Balance -->
                                    <div class="bg-white rounded-2xl p-5 border border-forest-100 shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1 group">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-cashdash-muted text-sm font-medium">Aktuellt saldo</span>
                                            <div class="w-8 h-8 rounded-lg bg-forest-50 flex items-center justify-center group-hover:bg-cashdash-forest transition-colors">
                                                <svg class="w-4 h-4 text-cashdash-forest group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="font-display text-2xl md:text-3xl font-bold text-cashdash-text">
                                            <span class="dashboard-counter-currency" data-target="847320">0</span> <span class="text-lg font-normal text-cashdash-muted">kr</span>
                                        </p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                            <span class="text-cashdash-muted text-xs">Uppdaterat just nu</span>
                                        </div>
                                    </div>

                                    <!-- Expected Incoming -->
                                    <div class="bg-white rounded-2xl p-5 border border-forest-100 shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1 group">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-cashdash-muted text-sm font-medium">Väntade inbetalningar</span>
                                            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-cashdash-success transition-colors">
                                                <svg class="w-4 h-4 text-cashdash-success group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="font-display text-2xl md:text-3xl font-bold text-cashdash-success">
                                            +<span class="dashboard-counter-currency" data-target="234500">0</span> <span class="text-lg font-normal text-cashdash-muted">kr</span>
                                        </p>
                                        <p class="text-cashdash-muted text-xs mt-2">Nästa 30 dagar • 8 fakturor</p>
                                    </div>

                                    <!-- Expected Outgoing -->
                                    <div class="bg-white rounded-2xl p-5 border border-forest-100 shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-1 group">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-cashdash-muted text-sm font-medium">Väntade utbetalningar</span>
                                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-500 transition-colors">
                                                <svg class="w-4 h-4 text-red-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="font-display text-2xl md:text-3xl font-bold text-red-500">
                                            -<span class="dashboard-counter-currency" data-target="156200">0</span> <span class="text-lg font-normal text-cashdash-muted">kr</span>
                                        </p>
                                        <p class="text-cashdash-muted text-xs mt-2">Nästa 30 dagar • 12 fakturor</p>
                                    </div>
                                </div>

                                <!-- Charts Row -->
                                <div class="grid lg:grid-cols-3 gap-4 md:gap-6 mb-6">
                                    <!-- Main Cash Flow Chart -->
                                    <div class="lg:col-span-2 bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                            <div>
                                                <h3 class="font-semibold text-cashdash-text text-lg">Kassaflödesprognos</h3>
                                                <p class="text-cashdash-muted text-sm">Baserat på historik och öppna fakturor</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-cashdash-forest text-white">12 mån</button>
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-forest-50 text-cashdash-forest hover:bg-forest-100 transition-colors">6 mån</button>
                                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-forest-50 text-cashdash-forest hover:bg-forest-100 transition-colors">3 mån</button>
                                            </div>
                                        </div>
                                        <div id="cashflow-chart" class="h-64 md:h-72"></div>
                                        <div class="flex items-center justify-center gap-6 mt-4 text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full bg-cashdash-forest"></div>
                                                <span class="text-cashdash-muted">Faktiskt</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full bg-cashdash-gold"></div>
                                                <span class="text-cashdash-muted">Prognos</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full bg-forest-200"></div>
                                                <span class="text-cashdash-muted">Min/Max</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Runway Radial Chart -->
                                    <div class="bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                                        <h3 class="font-semibold text-cashdash-text text-lg mb-1">Runway-status</h3>
                                        <p class="text-cashdash-muted text-sm mb-4">Hur länge räcker pengarna?</p>
                                        <div id="runway-radial-chart" class="h-48"></div>
                                        <div class="mt-4 space-y-2">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-cashdash-muted">Kritisk nivå</span>
                                                <span class="text-red-500 font-medium">&lt; 30 dagar</span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-cashdash-muted">Varning</span>
                                                <span class="text-yellow-500 font-medium">30-60 dagar</span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-cashdash-muted">Stabil</span>
                                                <span class="text-cashdash-success font-medium">&gt; 60 dagar ✓</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom Row -->
                                <div class="grid lg:grid-cols-2 gap-4 md:gap-6">
                                    <!-- AI Insights -->
                                    <div class="bg-gradient-to-br from-cashdash-forest/5 to-cashdash-gold/5 rounded-2xl p-5 md:p-6 border border-cashdash-forest/20">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cashdash-forest to-forest-600 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-cashdash-text">AI-drivna insikter</h3>
                                                <p class="text-cashdash-muted text-xs">Uppdaterad för 5 minuter sedan</p>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex items-start gap-3 bg-white/60 rounded-xl p-3 border border-cashdash-success/20">
                                                <div class="w-6 h-6 rounded-full bg-cashdash-success/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-cashdash-text"><strong>Stabilt kassaflöde.</strong> Din runway har ökat med 12 dagar senaste månaden.</p>
                                            </div>
                                            <div class="flex items-start gap-3 bg-white/60 rounded-xl p-3 border border-yellow-500/20">
                                                <div class="w-6 h-6 rounded-full bg-yellow-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-cashdash-text"><strong>2 förfallna fakturor</strong> på totalt 45 000 kr. Överväg påminnelse.</p>
                                            </div>
                                            <div class="flex items-start gap-3 bg-white/60 rounded-xl p-3 border border-cashdash-forest/20">
                                                <div class="w-6 h-6 rounded-full bg-cashdash-forest/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-cashdash-forest" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-cashdash-text"><strong>Kund "Byggteamet AB"</strong> betalar i snitt 8 dagar sent. Fakturera tidigare?</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Patterns Chart -->
                                    <div class="bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h3 class="font-semibold text-cashdash-text text-lg">Betalningsbeteende</h3>
                                                <p class="text-cashdash-muted text-sm">Genomsnittlig betalningstid per kund</p>
                                            </div>
                                        </div>
                                        <div id="payment-patterns-chart" class="h-48"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge -->
                        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-white rounded-full shadow-elevated px-6 py-3 flex items-center gap-3 border border-forest-100">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-forest-300 to-forest-500 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold-300 to-gold-500 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-forest-400 to-gold-400 border-2 border-white"></div>
                            </div>
                            <p class="text-sm text-cashdash-text">
                                <strong>{{ number_format($companyCount, 0, ',', ' ') }}+ företag</strong> använder CashDash
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ApexCharts for Dashboard -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize charts when section is visible
            const dashboardSection = document.getElementById('dashboard-preview-section');

            const observerCharts = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        initializeDashboardCharts();
                        animateDashboardCounters();
                        observerCharts.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            observerCharts.observe(dashboardSection);

            function animateDashboardCounters() {
                // Animate regular counters
                document.querySelectorAll('.dashboard-counter').forEach(counter => {
                    const target = parseInt(counter.dataset.target);
                    animateValue(counter, 0, target, 2000);
                });

                // Animate currency counters
                document.querySelectorAll('.dashboard-counter-currency').forEach(counter => {
                    const target = parseInt(counter.dataset.target);
                    animateValueWithFormat(counter, 0, target, 2000);
                });
            }

            function animateValue(el, start, end, duration) {
                let startTime = null;
                function animation(currentTime) {
                    if (!startTime) startTime = currentTime;
                    const progress = Math.min((currentTime - startTime) / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(easeOut * (end - start) + start);
                    if (progress < 1) requestAnimationFrame(animation);
                }
                requestAnimationFrame(animation);
            }

            function animateValueWithFormat(el, start, end, duration) {
                let startTime = null;
                function animation(currentTime) {
                    if (!startTime) startTime = currentTime;
                    const progress = Math.min((currentTime - startTime) / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const value = Math.floor(easeOut * (end - start) + start);
                    el.textContent = new Intl.NumberFormat('sv-SE').format(value);
                    if (progress < 1) requestAnimationFrame(animation);
                }
                requestAnimationFrame(animation);
            }

            function initializeDashboardCharts() {
                // Main Cash Flow Area Chart
                const cashflowOptions = {
                    series: [{
                        name: 'Faktiskt',
                        type: 'area',
                        data: [620000, 680000, 590000, 720000, 780000, 847320, null, null, null, null, null, null]
                    }, {
                        name: 'Prognos',
                        type: 'line',
                        data: [null, null, null, null, null, 847320, 890000, 920000, 880000, 950000, 1020000, 1080000]
                    }, {
                        name: 'Min',
                        type: 'area',
                        data: [null, null, null, null, null, null, 820000, 840000, 780000, 830000, 880000, 920000]
                    }, {
                        name: 'Max',
                        type: 'area',
                        data: [null, null, null, null, null, null, 960000, 1000000, 980000, 1070000, 1160000, 1240000]
                    }],
                    chart: {
                        height: '100%',
                        type: 'line',
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 1500,
                            animateGradually: { enabled: true, delay: 150 }
                        },
                        fontFamily: 'Inter, sans-serif',
                        dropShadow: {
                            enabled: true,
                            top: 3,
                            left: 0,
                            blur: 4,
                            opacity: 0.1
                        }
                    },
                    colors: ['#1A3D2E', '#C4A962', '#dceee5', '#dceee5'],
                    fill: {
                        type: ['gradient', 'solid', 'gradient', 'gradient'],
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.5,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 3, 0, 0],
                        dashArray: [0, 5, 0, 0]
                    },
                    xaxis: {
                        categories: ['Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun'],
                        labels: {
                            style: { colors: '#6B6B6B', fontSize: '12px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => val ? (val / 1000).toFixed(0) + ' tkr' : '',
                            style: { colors: '#6B6B6B', fontSize: '12px' }
                        }
                    },
                    grid: {
                        borderColor: '#f0f7f4',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => val ? new Intl.NumberFormat('sv-SE').format(val) + ' kr' : '-'
                        }
                    },
                    legend: { show: false },
                    markers: {
                        size: [4, 0, 0, 0],
                        colors: ['#1A3D2E'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: { size: 6 }
                    }
                };

                const cashflowChart = new ApexCharts(document.querySelector("#cashflow-chart"), cashflowOptions);
                cashflowChart.render();

                // Runway Radial Chart
                const runwayRadialOptions = {
                    series: [72], // 87 days out of 120 max = 72%
                    chart: {
                        height: '100%',
                        type: 'radialBar',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 2000
                        }
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: {
                                size: '65%',
                                background: 'transparent'
                            },
                            track: {
                                background: '#f0f7f4',
                                strokeWidth: '100%'
                            },
                            dataLabels: {
                                name: {
                                    fontSize: '14px',
                                    color: '#6B6B6B',
                                    offsetY: 60
                                },
                                value: {
                                    offsetY: -10,
                                    fontSize: '36px',
                                    fontWeight: 'bold',
                                    color: '#1A3D2E',
                                    formatter: function() { return '87'; }
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'horizontal',
                            gradientToColors: ['#C4A962'],
                            stops: [0, 100]
                        }
                    },
                    stroke: { lineCap: 'round' },
                    labels: ['dagar kvar'],
                    colors: ['#1A3D2E']
                };

                const runwayRadialChart = new ApexCharts(document.querySelector("#runway-radial-chart"), runwayRadialOptions);
                runwayRadialChart.render();

                // Payment Patterns Bar Chart
                const paymentPatternsOptions = {
                    series: [{
                        name: 'Dagar',
                        data: [3, 8, 12, 5, 15, 7]
                    }],
                    chart: {
                        type: 'bar',
                        height: '100%',
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 1200
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            horizontal: true,
                            distributed: true,
                            dataLabels: { position: 'top' }
                        }
                    },
                    colors: ['#2D7A4F', '#C4A962', '#D97706', '#2D7A4F', '#DC2626', '#1A3D2E'],
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val + ' dgr',
                        offsetX: 30,
                        style: { fontSize: '11px', colors: ['#6B6B6B'] }
                    },
                    xaxis: {
                        categories: ['Kund A', 'Kund B', 'Kund C', 'Kund D', 'Kund E', 'Kund F'],
                        labels: { show: false },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#6B6B6B', fontSize: '12px' }
                        }
                    },
                    grid: { show: false },
                    tooltip: {
                        y: { formatter: (val) => val + ' dagar efter förfallodatum' }
                    },
                    legend: { show: false }
                };

                const paymentPatternsChart = new ApexCharts(document.querySelector("#payment-patterns-chart"), paymentPatternsOptions);
                paymentPatternsChart.render();

                // Runway Sparkline
                const sparklineOptions = {
                    series: [{ data: [65, 68, 72, 75, 78, 80, 82, 85, 87] }],
                    chart: {
                        type: 'line',
                        height: 40,
                        width: 80,
                        sparkline: { enabled: true },
                        animations: { enabled: true, easing: 'easeinout', speed: 1500 }
                    },
                    stroke: { curve: 'smooth', width: 2 },
                    colors: ['#C4A962'],
                    tooltip: { enabled: false }
                };

                const sparkline = new ApexCharts(document.querySelector("#runway-sparkline"), sparklineOptions);
                sparkline.render();
            }
        });
        </script>

        <!-- ========================================
             6. PRICING
             ======================================== -->
        <section id="priser" class="section" aria-labelledby="pricing-heading">
            <div class="container-landing">
                <div class="section-header">
                    <h2 id="pricing-heading" class="section-title">Enkel prissattning</h2>
                    <p class="section-subtitle">En plan, alla funktioner, inga dolda avgifter</p>
                </div>

                <div class="max-w-lg mx-auto">
                    <div class="pricing-card">
                        <div class="pricing-badge">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
                            </svg>
                            14 dagars gratis provperiod
                        </div>

                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="pricing-amount">149</span>
                            <span class="pricing-period">kr/månad</span>
                        </div>

                        <p class="text-cashdash-muted mb-8">
                            Perfekt för småföretag och konsulter som vill ha full kontroll över sitt kassaflöde.
                        </p>

                        <ul class="space-y-4 mb-8" role="list">
                            @php
                                $features = [
                                    'Obegränsade Fortnox-synkroniseringar',
                                    'Realtidsuppdateringar av kassaposition',
                                    '12-månaders kassaflödesprognos',
                                    'AI-drivna insikter och varningar',
                                    'Analys av betalningsmönster',
                                    'Exportera rapporter till Excel/PDF',
                                    'Prioriterad kundsupport',
                                    'Bankgrad säkerhet'
                                ];
                            @endphp
                            @foreach($features as $feature)
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-cashdash-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-cashdash-text">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('register') }}" class="btn-gold w-full text-center text-lg">
                            Starta gratis provperiod
                        </a>

                        <p class="text-center text-sm text-cashdash-muted mt-4">
                            Inget kreditkort krävs. Avsluta när som helst.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             7. TESTIMONIALS
             ======================================== -->
        <section class="section bg-white" aria-labelledby="testimonials-heading">
            <div class="container-landing">
                <div class="section-header">
                    <h2 id="testimonials-heading" class="section-title">Företagare litar på CashDash</h2>
                    <p class="section-subtitle">Hör vad våra kunder säger</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <!-- Testimonial 1 -->
                    <article class="testimonial-card">
                        <blockquote class="testimonial-quote">
                            Fantastiskt verktyg! Jag har aldrig haft så bra koll på mitt kassaflöde förr. Sparar mig timmar varje vecka.
                        </blockquote>
                        <footer class="testimonial-author">
                            <div class="testimonial-avatar" aria-hidden="true">
                                <div class="w-full h-full bg-gradient-to-br from-forest-300 to-forest-500"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-cashdash-text">Anna L.</p>
                                <p class="text-sm text-cashdash-muted">VD</p>
                            </div>
                        </footer>
                    </article>

                    <!-- Testimonial 2 -->
                    <article class="testimonial-card">
                        <blockquote class="testimonial-quote">
                            Integrationen med Fortnox var smidig och nu ser jag direkt om någon faktura är försenad. Rekommenderas varmt!
                        </blockquote>
                        <footer class="testimonial-author">
                            <div class="testimonial-avatar" aria-hidden="true">
                                <div class="w-full h-full bg-gradient-to-br from-gold-300 to-gold-500"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-cashdash-text">Erik J.</p>
                                <p class="text-sm text-cashdash-muted">Grundare</p>
                            </div>
                        </footer>
                    </article>

                    <!-- Testimonial 3 -->
                    <article class="testimonial-card">
                        <blockquote class="testimonial-quote">
                            AI-prognoserna har hjälpt oss undvika flera likviditetskriser. Pengarna vi sparar är värda många gånger priset.
                        </blockquote>
                        <footer class="testimonial-author">
                            <div class="testimonial-avatar" aria-hidden="true">
                                <div class="w-full h-full bg-gradient-to-br from-forest-400 to-gold-400"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-cashdash-text">Maria S.</p>
                                <p class="text-sm text-cashdash-muted">CFO</p>
                            </div>
                        </footer>
                    </article>
                </div>
            </div>
        </section>

        <!-- ========================================
             8. FAQ SECTION
             ======================================== -->
        <section id="faq" class="section" aria-labelledby="faq-heading">
            <div class="container-landing">
                <div class="section-header">
                    <h2 id="faq-heading" class="section-title">Vanliga frågor</h2>
                    <p class="section-subtitle">Har du frågor? Vi har svaren.</p>
                </div>

                <div class="max-w-3xl mx-auto">
                    @php
                        $faqs = [
                            [
                                'question' => 'Hur säkerhetsskyddas min data?',
                                'answer' => 'När du skapar ett konto väljer du en lösenfras som låser upp din data. Tänk på det som att du lånar oss nyckeln till ett kassaskåp – vi kan visa innehållet för dig medan du är kund, men vi kan aldrig kopiera nyckeln eller öppna skåpet utan dig. När du avslutar din prenumeration försvinner lånet och kassaskåpet blir permanent låst – ingen, inte ens vi, kan någonsin öppna det igen. Din data krypteras med bankgradig AES-256-kryptering och lagras i svenska datacenter.'
                            ],
                            [
                                'question' => 'Hur fungerar Fortnox-integrationen?',
                                'answer' => 'Du kopplar ditt Fortnox-konto med ett klick via Fortnox egna inloggning. Vi hämtar sedan automatiskt relevanta data som fakturor, betalningar och kontosaldon. Du kan när som helst koppla bort integrationen.'
                            ],
                            [
                                'question' => 'Vad händer efter provperioden?',
                                'answer' => 'Efter 14 dagars gratis provperiod kan du välja att fortsätta med vår månadsplan på 149 kr. Om du inte vill fortsätta så avslutas kontot automatiskt – inga överraskningar eller dolda avgifter.'
                            ],
                            [
                                'question' => 'Kan jag exportera mina rapporter?',
                                'answer' => 'Ja! Du kan exportera alla rapporter och prognoser till både Excel och PDF-format. Perfekt för bokslut, styrelsemöten eller samtal med banken.'
                            ],
                            [
                                'question' => 'Hur ofta uppdateras min data?',
                                'answer' => 'Data synkroniseras automatiskt varje timme från Fortnox. Du kan också manuellt starta en synkronisering när som helst för att få de senaste siffrorna.'
                            ],
                            [
                                'question' => 'Fungerar det för mitt företag?',
                                'answer' => 'CashDash är perfekt för småföretag, konsulter, frilansare och startups som använder Fortnox. Om du har Fortnox och vill ha bättre koll på ditt kassaflöde, så är CashDash för dig.'
                            ],
                        ];
                    @endphp

                    <div class="space-y-0">
                        @foreach($faqs as $index => $faq)
                            <details class="faq-item group" {{ $index === 0 ? 'open' : '' }}>
                                <summary class="faq-question cursor-pointer list-none">
                                    <span>{{ $faq['question'] }}</span>
                                    <svg class="faq-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </summary>
                                <div class="faq-answer">
                                    {{ $faq['answer'] }}
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             9. FINAL CTA
             ======================================== -->
        <section class="cta-section" aria-labelledby="cta-heading">
            <div class="container-landing relative z-10">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 id="cta-heading" class="font-display text-display-mobile md:text-display text-white mb-6">
                        Redo att ta kontroll över ditt kassaflöde?
                    </h2>
                    <p class="text-forest-200 text-lg md:text-xl mb-10 max-w-2xl mx-auto">
                        Börja idag med 14 dagars gratis provperiod. Ingen bindningstid, inget kreditkort.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="btn-gold w-full sm:w-auto text-lg px-10">
                            Starta gratis nu
                        </a>
                        <a href="#hur-det-fungerar" class="inline-flex items-center gap-2 text-white hover:text-gold-300 transition-colors">
                            <span>Läs mer</span>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ========================================
         10. FOOTER
         ======================================== -->
    <footer class="footer" role="contentinfo">
        <div class="container-landing">
            <div class="grid md:grid-cols-4 gap-8 md:gap-12 mb-12">
                <!-- Brand Column -->
                <div class="md:col-span-1">
                    <a href="/" class="flex items-center gap-2 mb-4" aria-label="CashDash hem">
                        <img src="/images/logo.svg" alt="CashDash" class="w-10 h-10">
                        <span class="font-display font-bold text-xl text-white">Cash<span class="text-[#C4A962]">Dash</span></span>
                    </a>
                    <p class="text-forest-200 text-sm">
                        Din kassaflödesdashboard för svenska företag. Koppla Fortnox och få full kontroll.
                    </p>
                </div>

                <!-- Product Links -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Produkt</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#funktioner" class="footer-link">Funktioner</a></li>
                        <li><a href="#priser" class="footer-link">Priser</a></li>
                        <li><a href="#hur-det-fungerar" class="footer-link">Hur det fungerar</a></li>
                        <li><a href="#faq" class="footer-link">FAQ</a></li>
                    </ul>
                </div>

                <!-- Company Links -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Företag</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="/om-oss" class="footer-link">Om oss</a></li>
                        <li><a href="/blogg" class="footer-link">Blogg</a></li>
                        <li><a href="/karriar" class="footer-link">Karriär</a></li>
                        <li><a href="/kontakt" class="footer-link">Kontakt</a></li>
                    </ul>
                </div>

                <!-- Legal Links -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Juridiskt</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="/integritetspolicy" class="footer-link">Integritetspolicy</a></li>
                        <li><a href="/anvandarvillkor" class="footer-link">Användarvillkor</a></li>
                        <li><a href="/cookies" class="footer-link">Cookies</a></li>
                        <li><a href="/gdpr" class="footer-link">GDPR</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-forest-200 text-sm text-center md:text-left">
                    <p>&copy; {{ date('Y') }} Stafe Development AB. Alla rättigheter förbehållna.</p>
                    <p class="text-forest-300 text-xs mt-1">Blomstergatan 6, 591 70 Motala</p>
                </div>
                <div class="flex items-center gap-6">
                    <!-- Social Links -->
                    <a href="https://linkedin.com" class="footer-link" aria-label="Följ oss på LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com" class="footer-link" aria-label="Följ oss på X/Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Animations -->
    <script>
        // Navbar scroll behavior
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Animated Counter
        function animateCounter(element) {
            const target = parseInt(element.dataset.target);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current);
                }
            }, 16);
        }

        // Intersection Observer for counter animation
        const counterElement = document.getElementById('runway-days');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(counterElement);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(counterElement);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
