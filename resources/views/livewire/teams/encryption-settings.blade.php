<x-action-section>
    <x-slot name="title">
        {{ __('Säkerhet & Kryptering') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Hantera din krypteringsnyckel och säkerhetsinställningar.') }}
    </x-slot>

    <x-slot name="content">
        @if ($hasEncryption)
            <div class="space-y-6">
                {{-- Encryption Status --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isUnlocked ? 'bg-green-100' : 'bg-amber-100' }}">
                        @if ($isUnlocked)
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ $isUnlocked ? 'Kryptering aktiverad och upplåst' : 'Kryptering aktiverad men låst' }}
                        </p>
                        <p class="text-sm text-gray-500">AES-256 zero-knowledge kryptering</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-3">
                    @if ($isUnlocked)
                        <form action="{{ route('encryption.lock') }}" method="POST" class="inline">
                            @csrf
                            <x-secondary-button type="submit">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('Lås kryptering') }}
                            </x-secondary-button>
                        </form>

                        <a href="{{ route('encryption.change-passphrase') }}">
                            <x-secondary-button type="button">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ __('Ändra lösenfras') }}
                            </x-secondary-button>
                        </a>
                    @else
                        <a href="{{ route('encryption.unlock') }}">
                            <x-button type="button">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                {{ __('Lås upp kryptering') }}
                            </x-button>
                        </a>
                    @endif

                    <x-secondary-button type="button" wire:click="openDownloadModal">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ __('Ladda ner återställningsdokument') }}
                    </x-secondary-button>
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-blue-800">
                            <strong>Zero-knowledge:</strong> Vi kan aldrig se eller återställa din lösenfras.
                            Spara återställningsdokumentet på en säker plats.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- No encryption set up --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 text-gray-500">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Kryptering ej aktiverad</p>
                        <p class="text-sm">Aktiveras automatiskt när du kopplar Fortnox.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Download Modal --}}
        <x-dialog-modal wire:model.live="showDownloadModal">
            <x-slot name="title">
                {{ __('Ladda ner återställningsdokument') }}
            </x-slot>

            <x-slot name="content">
                <p class="text-sm text-gray-600 mb-4">
                    Ange din lösenfras för att ladda ner återställningsdokumentet.
                    Detta dokument innehåller din lösenfras och är viktigt för att återställa åtkomst till din data.
                </p>

                <div class="mt-4">
                    <x-label for="passphrase" value="{{ __('Lösenfras') }}" />
                    <x-input id="passphrase" type="password" class="mt-1 block w-full" wire:model="passphrase" placeholder="Din krypteringslösenfras" />
                    <x-input-error for="passphrase" class="mt-2" />
                </div>

                <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <p class="text-xs text-amber-700">
                        <strong>Varning:</strong> Förvara dokumentet säkert. Den som har tillgång till dokumentet kan dekryptera din data.
                    </p>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeDownloadModal">
                    {{ __('Avbryt') }}
                </x-secondary-button>

                <x-button class="ml-3" wire:click="downloadRecoveryPdf" wire:loading.attr="disabled">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('Ladda ner PDF') }}
                </x-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
