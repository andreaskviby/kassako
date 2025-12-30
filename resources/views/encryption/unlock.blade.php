<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lås upp kryptering - CashDash</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:500,600,700,800" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-cashdash-cream via-white to-forest-50">
    <div class="min-h-screen flex flex-col" x-data="{ isSubmitting: false, showPassphrase: false }">

        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-md border-b border-forest-100">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <img src="/images/logo.svg" alt="CashDash" class="w-10 h-10">
                    <span class="font-display font-bold text-xl text-cashdash-forest">Cash<span class="text-[#C4A962]">Dash</span></span>
                </a>
                <span class="text-sm text-cashdash-muted">{{ $team->name }}</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-4 md:p-8">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl border border-forest-100 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-cashdash-forest to-forest-600 p-6 md:p-8 text-white text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-display font-bold mb-2">Lås upp din data</h1>
                        <p class="text-white/80 text-sm">Ange din lösenfras för att komma åt {{ $team->name }}</p>
                    </div>

                    <!-- Content -->
                    <div class="p-6 md:p-8">
                        @if(session('warning'))
                            <div class="mb-6 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                                </div>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Security Info -->
                        <div class="bg-gradient-to-r from-cashdash-gold/10 to-cashdash-gold/5 rounded-xl p-4 mb-6 border border-cashdash-gold/20">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 bg-cashdash-gold/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-cashdash-gold" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-cashdash-text">
                                        <strong>Din data synkroniseras</strong> från Fortnox och krypteras med din nyckel. Sessionen är giltig i <strong>60 minuter</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('encryption.unlock.store') }}" @submit="isSubmitting = true">
                            @csrf

                            <div class="mb-6">
                                <label for="passphrase" class="block text-sm font-medium text-cashdash-text mb-2">Lösenfras</label>
                                <div class="relative">
                                    <input
                                        id="passphrase"
                                        :type="showPassphrase ? 'text' : 'password'"
                                        name="passphrase"
                                        required
                                        autofocus
                                        autocomplete="current-password"
                                        class="w-full px-4 py-3.5 rounded-xl border-2 border-forest-200 focus:border-cashdash-forest focus:ring-cashdash-forest/20 transition-all duration-200 text-lg"
                                        placeholder="Ange din lösenfras..."
                                    >
                                    <button type="button" @click="showPassphrase = !showPassphrase"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-cashdash-muted hover:text-cashdash-forest transition-colors">
                                        <svg x-show="!showPassphrase" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassphrase" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @error('passphrase')
                                <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-200">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror

                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="w-full py-4 px-6 rounded-xl font-semibold text-white transition-all duration-200 flex items-center justify-center gap-2 bg-cashdash-forest hover:bg-forest-700 shadow-lg shadow-cashdash-forest/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="isSubmitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="isSubmitting ? 'Låser upp...' : 'Lås upp och synkronisera'"></span>
                            </button>
                        </form>

                        <!-- Help Text -->
                        <div class="mt-6 pt-6 border-t border-forest-100">
                            <div class="bg-forest-50 rounded-xl p-4">
                                <h4 class="font-medium text-cashdash-text text-sm mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-cashdash-forest" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Tips för lösenfras
                                </h4>
                                <ul class="text-xs text-cashdash-muted space-y-1">
                                    <li>• Använd en mening du lätt minns: "MinHund2Springer!"</li>
                                    <li>• Eller spara i lösenordshanterare (1Password, Bitwarden)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Forgot Passphrase -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-cashdash-muted">
                                Glömt lösenfrasen? Tyvärr kan krypterad data inte återställas utan den ursprungliga lösenfrasen. Kontrollera ditt återställningsdokument.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center">
            <p class="text-sm text-cashdash-muted">
                En tjänst från <strong>Stafe Development AB</strong> · AES-256 kryptering · Svensk datalagring
            </p>
        </footer>
    </div>
</body>
</html>
