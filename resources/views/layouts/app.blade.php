<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} - CashDash</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="CashDash - Din kassaflödesdashboard för svenska företag. Se hur länge dina pengar räcker.">
        <meta name="theme-color" content="#1A3D2E">
        <meta name="robots" content="noindex, nofollow">

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
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <!-- Global Session Timer -->
        <script>
        function sessionTimer(expiresAt) {
            return {
                expiresAt: new Date(expiresAt),
                timeRemaining: '',
                interval: null,

                startTimer() {
                    this.updateTimeRemaining();
                    this.interval = setInterval(() => {
                        this.updateTimeRemaining();
                    }, 1000);
                },

                updateTimeRemaining() {
                    const now = new Date();
                    const diff = this.expiresAt - now;

                    if (diff <= 0) {
                        this.timeRemaining = 'Utgången';
                        clearInterval(this.interval);
                        window.location.href = '/encryption/unlock';
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);

                    this.timeRemaining = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
            };
        }
        </script>

        @stack('scripts')
    </body>
</html>
