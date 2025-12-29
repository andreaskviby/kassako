<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Säkerhetsinstallation - CashDash</title>

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
    <div class="min-h-screen flex flex-col"
         x-data="{
             step: 1,
             passphrase: '',
             confirmation: '',
             hasDownloaded: false,
             hasConfirmedSave: false,
             hasConfirmedUnderstand: false,
             isSubmitting: false,
             showPassphrase: false,
             get hasMinLength() { return this.passphrase.length >= 12 },
             get hasUppercase() { return /[A-Z]/.test(this.passphrase) },
             get hasLowercase() { return /[a-z]/.test(this.passphrase) },
             get hasNumber() { return /[0-9]/.test(this.passphrase) },
             get isValid() { return this.hasMinLength && this.hasUppercase && this.hasLowercase && this.hasNumber },
             get confirmationMatches() { return this.passphrase.length > 0 && this.passphrase === this.confirmation },
             get canProceedStep2() { return this.isValid && this.confirmationMatches },
             get canProceedStep3() { return this.hasDownloaded },
             get canSubmit() { return this.canProceedStep2 && this.hasDownloaded && this.hasConfirmedSave && this.hasConfirmedUnderstand },
             downloadPdf() {
                 const form = document.createElement('form');
                 form.method = 'POST';
                 form.action = '{{ route('encryption.download-recovery') }}';
                 form.target = '_blank';

                 const csrf = document.createElement('input');
                 csrf.type = 'hidden';
                 csrf.name = '_token';
                 csrf.value = '{{ csrf_token() }}';
                 form.appendChild(csrf);

                 const pass = document.createElement('input');
                 pass.type = 'hidden';
                 pass.name = 'passphrase';
                 pass.value = this.passphrase;
                 form.appendChild(pass);

                 document.body.appendChild(form);
                 form.submit();
                 document.body.removeChild(form);

                 this.hasDownloaded = true;
             }
         }">

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
            <div class="w-full max-w-2xl">

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-center gap-2 md:gap-4">
                        <template x-for="s in 3" :key="s">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full font-semibold text-sm transition-all duration-300"
                                     :class="{
                                         'bg-cashdash-forest text-white shadow-lg shadow-cashdash-forest/30': step >= s,
                                         'bg-forest-100 text-cashdash-muted': step < s
                                     }">
                                    <span x-show="step <= s" x-text="s"></span>
                                    <svg x-show="step > s" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div x-show="s < 3" class="w-12 md:w-24 h-1 mx-2 rounded-full transition-all duration-300"
                                     :class="step > s ? 'bg-cashdash-forest' : 'bg-forest-100'"></div>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-center gap-8 md:gap-20 mt-3">
                        <span class="text-xs md:text-sm text-cashdash-muted">Skapa lösenfras</span>
                        <span class="text-xs md:text-sm text-cashdash-muted">Spara säkert</span>
                        <span class="text-xs md:text-sm text-cashdash-muted">Aktivera</span>
                    </div>
                </div>

                <!-- Step 1: Create Passphrase -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="bg-white rounded-3xl shadow-xl border border-forest-100 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-cashdash-forest to-forest-600 p-6 md:p-8 text-white">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl md:text-3xl font-display font-bold">Skapa din lösenfras</h1>
                                    <p class="text-white/80">Din nyckel till kassaskåpet</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 md:p-8">
                            <!-- Explanation -->
                            <div class="bg-gradient-to-r from-cashdash-gold/10 to-cashdash-gold/5 rounded-2xl p-5 mb-6 border border-cashdash-gold/20">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 bg-cashdash-gold/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-cashdash-gold" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-cashdash-text mb-1">Så fungerar det</h3>
                                        <p class="text-sm text-cashdash-muted leading-relaxed">
                                            Din lösenfras skapar en unik krypteringsnyckel som skyddar all din finansiella data.
                                            <strong class="text-cashdash-text">Vi kan aldrig se eller återställa din lösenfras</strong> –
                                            det är det som gör dina data säkra.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Passphrase Input -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-cashdash-text mb-2">Lösenfras</label>
                                    <div class="relative">
                                        <input
                                            :type="showPassphrase ? 'text' : 'password'"
                                            x-model="passphrase"
                                            class="w-full px-4 py-3.5 rounded-xl border-2 transition-all duration-200 text-lg"
                                            :class="{
                                                'border-cashdash-success bg-cashdash-success/5 focus:ring-cashdash-success/20': isValid,
                                                'border-forest-200 focus:border-cashdash-forest focus:ring-cashdash-forest/20': !isValid
                                            }"
                                            placeholder="Minst 12 tecken..."
                                            autocomplete="new-password"
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

                                    <!-- Strength Indicators -->
                                    <div class="mt-4 grid grid-cols-2 gap-2" x-show="passphrase.length > 0" x-cloak>
                                        <div class="flex items-center gap-2 p-2 rounded-lg transition-all"
                                             :class="hasMinLength ? 'bg-cashdash-success/10 text-cashdash-success' : 'bg-gray-50 text-cashdash-muted'">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path x-show="hasMinLength" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                <path x-show="!hasMinLength" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-medium">12+ tecken (<span x-text="passphrase.length"></span>)</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2 rounded-lg transition-all"
                                             :class="hasUppercase ? 'bg-cashdash-success/10 text-cashdash-success' : 'bg-gray-50 text-cashdash-muted'">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path x-show="hasUppercase" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                <path x-show="!hasUppercase" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-medium">Stor bokstav (A-Z)</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2 rounded-lg transition-all"
                                             :class="hasLowercase ? 'bg-cashdash-success/10 text-cashdash-success' : 'bg-gray-50 text-cashdash-muted'">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path x-show="hasLowercase" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                <path x-show="!hasLowercase" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-medium">Liten bokstav (a-z)</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2 rounded-lg transition-all"
                                             :class="hasNumber ? 'bg-cashdash-success/10 text-cashdash-success' : 'bg-gray-50 text-cashdash-muted'">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path x-show="hasNumber" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                <path x-show="!hasNumber" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-medium">Siffra (0-9)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation -->
                                <div x-show="isValid" x-cloak x-transition>
                                    <label class="block text-sm font-medium text-cashdash-text mb-2">Bekräfta lösenfras</label>
                                    <div class="relative">
                                        <input
                                            :type="showPassphrase ? 'text' : 'password'"
                                            x-model="confirmation"
                                            class="w-full px-4 py-3.5 rounded-xl border-2 transition-all duration-200 text-lg"
                                            :class="{
                                                'border-cashdash-success bg-cashdash-success/5': confirmationMatches,
                                                'border-red-300 bg-red-50': confirmation.length > 0 && !confirmationMatches,
                                                'border-forest-200': confirmation.length === 0
                                            }"
                                            placeholder="Skriv lösenfrasen igen..."
                                            autocomplete="new-password"
                                        >
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <svg x-show="confirmationMatches" x-cloak class="w-6 h-6 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="confirmation.length > 0 && !confirmationMatches" x-cloak class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p x-show="confirmation.length > 0 && !confirmationMatches" x-cloak class="mt-2 text-sm text-red-500">
                                        Lösenfraserna matchar inte
                                    </p>
                                </div>
                            </div>

                            <!-- Next Button -->
                            <div class="mt-8">
                                <button type="button"
                                        @click="step = 2"
                                        :disabled="!canProceedStep2"
                                        class="w-full py-4 px-6 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2"
                                        :class="canProceedStep2 ? 'bg-[#1A3D2E] hover:bg-[#2D5A45] text-white shadow-lg' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                    Fortsätt till säker lagring
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Download Recovery Document -->
                <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="bg-white rounded-3xl shadow-xl border border-forest-100 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-cashdash-gold to-yellow-500 p-6 md:p-8 text-white">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl md:text-3xl font-display font-bold">Spara ditt återställningsdokument</h1>
                                    <p class="text-white/80">Obligatoriskt steg för din säkerhet</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 md:p-8">
                            <!-- Warning -->
                            <div class="bg-red-50 rounded-2xl p-5 mb-6 border border-red-200">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-red-800 mb-1">Viktigt: Utan detta dokument kan din data gå förlorad</h3>
                                        <p class="text-sm text-red-700 leading-relaxed">
                                            Om du glömmer din lösenfras och inte har detta dokument kan vi <strong>inte</strong> återställa din data.
                                            Ladda ner och spara dokumentet på en säker plats.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Section -->
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-cashdash-forest/10 rounded-3xl mb-6"
                                     :class="hasDownloaded ? 'bg-cashdash-success/10' : ''">
                                    <svg x-show="!hasDownloaded" class="w-10 h-10 text-cashdash-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <svg x-show="hasDownloaded" x-cloak class="w-10 h-10 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>

                                <h3 class="text-xl font-semibold text-cashdash-text mb-2" x-text="hasDownloaded ? 'Dokument nedladdat!' : 'Ladda ner återställningsdokumentet'"></h3>
                                <p class="text-cashdash-muted mb-6 max-w-md mx-auto" x-text="hasDownloaded ? 'Spara filen på en säker plats som du kommer ihåg.' : 'Dokumentet innehåller din lösenfras och instruktioner för återställning.'"></p>

                                <button type="button"
                                        @click="downloadPdf()"
                                        class="inline-flex items-center gap-3 px-8 py-4 rounded-xl font-semibold transition-all duration-200"
                                        :class="hasDownloaded ? 'bg-cashdash-success/10 text-cashdash-success hover:bg-cashdash-success/20' : 'bg-cashdash-forest text-white hover:bg-forest-700 shadow-lg shadow-cashdash-forest/30'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span x-text="hasDownloaded ? 'Ladda ner igen' : 'Ladda ner PDF'"></span>
                                </button>
                            </div>

                            <!-- Storage Tips -->
                            <div class="bg-forest-50 rounded-2xl p-5 mt-6">
                                <h4 class="font-semibold text-cashdash-text mb-3">Rekommenderade lagringsplatser:</h4>
                                <div class="grid md:grid-cols-2 gap-3">
                                    <div class="flex items-center gap-3 text-sm text-cashdash-muted">
                                        <span class="text-cashdash-success">✓</span>
                                        Lösenordshanterare (1Password, Bitwarden)
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-cashdash-muted">
                                        <span class="text-cashdash-success">✓</span>
                                        Krypterad molnlagring
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-cashdash-muted">
                                        <span class="text-cashdash-success">✓</span>
                                        USB-minne i kassaskåp
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-cashdash-muted">
                                        <span class="text-cashdash-success">✓</span>
                                        Utskrivet i bankfack
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="mt-8 flex gap-4">
                                <button type="button"
                                        @click="step = 1"
                                        class="flex-1 py-4 px-6 rounded-xl font-semibold border-2 border-forest-200 text-cashdash-muted hover:border-cashdash-forest hover:text-cashdash-forest transition-all duration-200">
                                    Tillbaka
                                </button>
                                <button type="button"
                                        @click="step = 3"
                                        :disabled="!hasDownloaded"
                                        class="flex-1 py-4 px-6 rounded-xl font-semibold text-white transition-all duration-200 flex items-center justify-center gap-2"
                                        :class="hasDownloaded ? 'bg-cashdash-forest hover:bg-forest-700 shadow-lg shadow-cashdash-forest/30' : 'bg-gray-300 cursor-not-allowed'">
                                    Fortsätt till aktivering
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Confirm and Activate -->
                <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <form method="POST" action="{{ route('encryption.setup.store') }}" @submit="isSubmitting = true">
                        @csrf
                        <input type="hidden" name="passphrase" :value="passphrase">
                        <input type="hidden" name="passphrase_confirmation" :value="confirmation">

                        <div class="bg-white rounded-3xl shadow-xl border border-forest-100 overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-cashdash-success to-green-600 p-6 md:p-8 text-white">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-2xl md:text-3xl font-display font-bold">Aktivera kryptering</h1>
                                        <p class="text-white/80">Sista steget för att skydda din data</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 md:p-8">
                                <!-- Summary -->
                                <div class="bg-forest-50 rounded-2xl p-5 mb-6">
                                    <h4 class="font-semibold text-cashdash-text mb-4">Sammanfattning</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-cashdash-success/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-cashdash-text">Stark lösenfras skapad</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-cashdash-success/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-cashdash-text">Återställningsdokument nedladdat</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-cashdash-forest/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-cashdash-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </div>
                                            <span class="text-cashdash-text">AES-256 kryptering klar att aktiveras</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmations -->
                                <div class="space-y-4 mb-8">
                                    <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                           :class="hasConfirmedSave ? 'border-cashdash-success bg-cashdash-success/5' : 'border-forest-200 hover:border-cashdash-forest'">
                                        <input type="checkbox" x-model="hasConfirmedSave" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-cashdash-forest focus:ring-cashdash-forest">
                                        <div>
                                            <span class="font-medium text-cashdash-text">Jag har sparat återställningsdokumentet</span>
                                            <p class="text-sm text-cashdash-muted mt-1">Jag har laddat ner och sparat dokumentet på en säker plats.</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                           :class="hasConfirmedUnderstand ? 'border-cashdash-success bg-cashdash-success/5' : 'border-forest-200 hover:border-cashdash-forest'">
                                        <input type="checkbox" x-model="hasConfirmedUnderstand" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-cashdash-forest focus:ring-cashdash-forest">
                                        <div>
                                            <span class="font-medium text-cashdash-text">Jag förstår att min data inte kan återställas utan lösenfrasen</span>
                                            <p class="text-sm text-cashdash-muted mt-1">CashDash kan inte återställa eller se min lösenfras. Om jag glömmer den och inte har dokumentet är min data förlorad för alltid.</p>
                                        </div>
                                    </label>
                                </div>

                                @error('passphrase')
                                    <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-200">
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    </div>
                                @enderror

                                <!-- Navigation -->
                                <div class="flex gap-4">
                                    <button type="button"
                                            @click="step = 2"
                                            class="flex-1 py-4 px-6 rounded-xl font-semibold border-2 border-forest-200 text-cashdash-muted hover:border-cashdash-forest hover:text-cashdash-forest transition-all duration-200">
                                        Tillbaka
                                    </button>
                                    <button type="submit"
                                            :disabled="!canSubmit || isSubmitting"
                                            class="flex-1 py-4 px-6 rounded-xl font-semibold text-white transition-all duration-200 flex items-center justify-center gap-2"
                                            :class="canSubmit && !isSubmitting ? 'bg-cashdash-success hover:bg-green-700 shadow-lg shadow-cashdash-success/30' : 'bg-gray-300 cursor-not-allowed'">
                                        <svg x-show="isSubmitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span x-text="isSubmitting ? 'Aktiverar...' : 'Aktivera kryptering'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center">
            <p class="text-sm text-cashdash-muted">
                En tjänst från <strong>Stafe Group AB</strong> · AES-256 kryptering · Svensk datalagring
            </p>
        </footer>
    </div>
</body>
</html>
