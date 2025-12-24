<x-action-section>
    <x-slot name="title">
        {{ __('Fortnox-koppling') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Koppla ditt Fortnox-konto för att automatiskt hämta fakturor och ekonomisk data.') }}
    </x-slot>

    <x-slot name="content">
        @if ($hasFortnox)
            {{-- Connected State --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Fortnox är kopplat</p>
                        @if ($lastSyncAt)
                            <p class="text-sm text-gray-500">Senast synkat: {{ $lastSyncAt }}</p>
                        @endif
                    </div>
                </div>

                @if ($syncStatus === 'syncing')
                    <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 rounded-lg px-4 py-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Synkroniserar data...
                    </div>
                @endif

                <div class="pt-4">
                    <x-danger-button wire:click="disconnect" wire:confirm="Är du säker på att du vill koppla bort Fortnox? All synkad data kommer att tas bort.">
                        {{ __('Koppla bort Fortnox') }}
                    </x-danger-button>
                </div>
            </div>
        @else
            {{-- Not Connected State --}}
            <div class="space-y-6">
                @if (!$hasEncryption)
                    {{-- Encryption not set up --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-amber-800 mb-1">Kryptering krävs</h4>
                                <p class="text-sm text-amber-700 mb-4">
                                    För att skydda din finansiella data från Fortnox behöver du först skapa en krypteringsnyckel.
                                    Detta säkerställer att ingen - inte ens vi - kan se din känsliga information.
                                </p>
                                <a href="{{ route('encryption.setup') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-lg hover:bg-amber-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Skapa krypteringsnyckel
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif (!$isEncryptionUnlocked)
                    {{-- Encryption locked --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-blue-800 mb-1">Kryptering låst</h4>
                                <p class="text-sm text-blue-700 mb-4">
                                    Lås upp krypteringen med din lösenfras för att koppla Fortnox.
                                </p>
                                <a href="{{ route('encryption.unlock') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Lås upp kryptering
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Ready to connect --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-sm text-green-600 bg-green-50 rounded-lg px-4 py-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Kryptering aktiverad och upplåst
                        </div>

                        <p class="text-sm text-gray-600">
                            Anslut ditt Fortnox-konto för att automatiskt importera fakturor, leverantörsskulder och banksaldo.
                            All data krypteras med din personliga nyckel.
                        </p>

                        <a href="{{ route('fortnox.connect') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-cashdash-forest text-white font-semibold rounded-lg hover:bg-forest-700 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Koppla Fortnox
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </x-slot>
</x-action-section>
