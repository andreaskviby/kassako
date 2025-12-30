<x-app-layout>
    <div class="min-h-screen bg-[#FDFBF7] py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <h1 class="font-serif text-3xl text-[#1C1C1C] mb-8">Fakturering</h1>

            {{-- Free Period Notice --}}
            <div class="bg-white rounded-2xl border border-[#F5F0E8] p-8 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-cashdash-forest to-forest-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>

                <h2 class="font-semibold text-xl text-cashdash-text mb-3">Gratis under lanseringsperioden!</h2>

                <p class="text-cashdash-muted mb-6 max-w-md mx-auto">
                    CashDash är helt gratis att använda under januari och februari 2026.
                    Ingen betalning krävs, inga begränsningar.
                </p>

                <div class="inline-flex items-center gap-2 bg-cashdash-gold/20 text-cashdash-forest font-medium px-6 py-3 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Gratis t.o.m. 28 februari 2026
                </div>

                <div class="mt-8 pt-6 border-t border-forest-100">
                    <p class="text-sm text-cashdash-muted mb-2">Efter lanseringsperioden</p>
                    <p class="text-cashdash-text">
                        <span class="font-semibold text-2xl">149 kr</span>
                        <span class="text-cashdash-muted">/månad</span>
                    </p>
                    <p class="text-xs text-cashdash-muted mt-1">Exkl. moms. Avsluta när som helst.</p>
                </div>
            </div>

            {{-- Features included --}}
            <div class="bg-white rounded-2xl border border-[#F5F0E8] p-6 mt-6">
                <h3 class="font-medium text-cashdash-text mb-4">Allt ingår under gratisperioden</h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm text-cashdash-muted">
                        <svg class="w-5 h-5 text-cashdash-success flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Obegränsad synkning med Fortnox
                    </li>
                    <li class="flex items-center gap-3 text-sm text-cashdash-muted">
                        <svg class="w-5 h-5 text-cashdash-success flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        AI-drivna kassaflödesinsikter
                    </li>
                    <li class="flex items-center gap-3 text-sm text-cashdash-muted">
                        <svg class="w-5 h-5 text-cashdash-success flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        12 månaders kassaflödesprognos
                    </li>
                    <li class="flex items-center gap-3 text-sm text-cashdash-muted">
                        <svg class="w-5 h-5 text-cashdash-success flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Kundbetalningsanalys
                    </li>
                    <li class="flex items-center gap-3 text-sm text-cashdash-muted">
                        <svg class="w-5 h-5 text-cashdash-success flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Zero-knowledge kryptering (AES-256)
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
