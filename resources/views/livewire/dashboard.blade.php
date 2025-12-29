<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Status Bar --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                @if($lastUpdated)
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-sm text-cashdash-muted">
                            Uppdaterad {{ $lastUpdated }}
                        </span>
                    </div>
                @endif
            </div>

            <button
                wire:click="refreshData"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-forest-50 hover:bg-forest-100 transition-colors disabled:opacity-50 text-sm font-medium text-cashdash-forest"
                title="Uppdatera data"
            >
                <svg
                    class="w-4 h-4"
                    wire:loading.class="animate-spin"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span wire:loading.remove>Uppdatera</span>
                <span wire:loading>Uppdaterar...</span>
            </button>
        </div>
        @if(!$hasConnection)
            {{-- No Fortnox Connection --}}
            <div class="bg-white rounded-2xl border border-forest-100 shadow-card p-8 md:p-12 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-gradient-to-br from-forest-50 to-cashdash-gold/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-cashdash-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h2 class="font-display text-3xl text-cashdash-text mb-3">Koppla Fortnox</h2>
                <p class="text-cashdash-muted mb-8 max-w-md mx-auto leading-relaxed">
                    Anslut ditt Fortnox-konto för att se din kassaprognos och få AI-drivna insikter. Det tar bara 30 sekunder.
                </p>
                <a
                    href="{{ route('fortnox.connect') }}"
                    class="btn-primary inline-flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Koppla Fortnox nu
                </a>
            </div>

        @elseif($isLoading)
            {{-- Loading State --}}
            <div class="bg-white rounded-2xl border border-forest-100 shadow-card p-12 md:p-16 text-center">
                <div class="animate-spin w-10 h-10 border-3 border-cashdash-forest border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-cashdash-muted text-lg">Laddar din kassaprognos...</p>
            </div>

        @elseif($snapshot)
            {{-- Dashboard Content - Matching Landing Page Preview --}}
            <div class="space-y-6">
                {{-- Top Stats Row --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    {{-- Runway Days - Hero Stat --}}
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
                                <span class="font-display text-4xl md:text-5xl font-bold" x-data="{ count: 0 }" x-init="
                                    let target = {{ $snapshot->runway_days }};
                                    let duration = 2000;
                                    let start = performance.now();
                                    function animate(now) {
                                        let progress = Math.min((now - start) / duration, 1);
                                        let easeOut = 1 - Math.pow(1 - progress, 3);
                                        count = Math.floor(easeOut * target);
                                        if (progress < 1) requestAnimationFrame(animate);
                                    }
                                    requestAnimationFrame(animate);
                                " x-text="count">0</span>
                                <span class="text-cashdash-gold font-semibold text-lg">dagar</span>
                            </div>
                            <div class="flex items-center gap-1 mt-3 text-green-300 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>+{{ $this->runwayTrend['change'] }} dagar sedan förra veckan</span>
                            </div>
                        </div>
                        {{-- Mini Sparkline --}}
                        <div id="runway-sparkline" class="absolute bottom-2 right-2 w-20 h-10 opacity-50"></div>
                    </div>

                    {{-- Current Balance --}}
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
                            <span x-data="{ count: 0 }" x-init="
                                let target = {{ $snapshot->cash_balance }};
                                let duration = 2000;
                                let start = performance.now();
                                function animate(now) {
                                    let progress = Math.min((now - start) / duration, 1);
                                    let easeOut = 1 - Math.pow(1 - progress, 3);
                                    count = Math.floor(easeOut * target);
                                    if (progress < 1) requestAnimationFrame(animate);
                                }
                                requestAnimationFrame(animate);
                            " x-text="new Intl.NumberFormat('sv-SE').format(count)">0</span>
                            <span class="text-lg font-normal text-cashdash-muted">kr</span>
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-cashdash-muted text-xs">Uppdaterat just nu</span>
                        </div>
                    </div>

                    {{-- Expected Incoming --}}
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
                            +<span x-data="{ count: 0 }" x-init="
                                let target = {{ $snapshot->accounts_receivable }};
                                let duration = 2000;
                                let start = performance.now();
                                function animate(now) {
                                    let progress = Math.min((now - start) / duration, 1);
                                    let easeOut = 1 - Math.pow(1 - progress, 3);
                                    count = Math.floor(easeOut * target);
                                    if (progress < 1) requestAnimationFrame(animate);
                                }
                                requestAnimationFrame(animate);
                            " x-text="new Intl.NumberFormat('sv-SE').format(count)">0</span>
                            <span class="text-lg font-normal text-cashdash-muted">kr</span>
                        </p>
                        <p class="text-cashdash-muted text-xs mt-2">Nästa 30 dagar</p>
                    </div>

                    {{-- Expected Outgoing --}}
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
                            -<span x-data="{ count: 0 }" x-init="
                                let target = {{ $snapshot->accounts_payable }};
                                let duration = 2000;
                                let start = performance.now();
                                function animate(now) {
                                    let progress = Math.min((now - start) / duration, 1);
                                    let easeOut = 1 - Math.pow(1 - progress, 3);
                                    count = Math.floor(easeOut * target);
                                    if (progress < 1) requestAnimationFrame(animate);
                                }
                                requestAnimationFrame(animate);
                            " x-text="new Intl.NumberFormat('sv-SE').format(count)">0</span>
                            <span class="text-lg font-normal text-cashdash-muted">kr</span>
                        </p>
                        <p class="text-cashdash-muted text-xs mt-2">Nästa 30 dagar</p>
                    </div>
                </div>

                {{-- Outstanding Invoices Section --}}
                <div class="bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-semibold text-cashdash-text text-lg">Utestående fakturor</h3>
                            <p class="text-cashdash-muted text-sm">Obetalda och förfallna kundfordringar</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        {{-- Unpaid Invoices --}}
                        <div class="bg-forest-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-cashdash-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-cashdash-muted text-xs font-medium">Obetalda</span>
                            </div>
                            <p class="font-display text-2xl font-bold text-cashdash-forest">{{ $this->outstandingInvoices['unpaid_count'] }}</p>
                            <p class="text-cashdash-muted text-xs">{{ number_format($this->outstandingInvoices['unpaid_total'], 0, ',', ' ') }} kr</p>
                        </div>

                        {{-- Overdue Invoices --}}
                        <div class="bg-red-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-cashdash-muted text-xs font-medium">Förfallna</span>
                            </div>
                            <p class="font-display text-2xl font-bold text-red-500">{{ $this->outstandingInvoices['overdue_count'] }}</p>
                            <p class="text-cashdash-muted text-xs">{{ number_format($this->outstandingInvoices['overdue_total'], 0, ',', ' ') }} kr</p>
                        </div>

                        {{-- Total Outstanding --}}
                        <div class="bg-cashdash-gold/10 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-cashdash-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-cashdash-muted text-xs font-medium">Totalt utestående</span>
                            </div>
                            <p class="font-display text-2xl font-bold text-cashdash-text">{{ $this->outstandingInvoices['total_count'] }}</p>
                            <p class="text-cashdash-muted text-xs">{{ number_format($this->outstandingInvoices['total_amount'], 0, ',', ' ') }} kr</p>
                        </div>

                        {{-- Average Days --}}
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-cashdash-muted text-xs font-medium">Åldringsanalys</span>
                            </div>
                            <div class="space-y-1 mt-2">
                                @foreach($this->agingAnalysis as $period => $data)
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full {{ $period === '90+' ? 'bg-red-500' : ($period === '61-90' ? 'bg-orange-500' : ($period === '31-60' ? 'bg-yellow-500' : 'bg-cashdash-forest')) }}"
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-cashdash-muted w-16">{{ $period }}d</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Overdue Invoices List --}}
                    @if(count($this->overdueInvoicesList) > 0)
                        <div class="border-t border-forest-100 pt-4">
                            <h4 class="text-sm font-medium text-cashdash-text mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Förfallna fakturor som kräver åtgärd
                            </h4>
                            <div class="space-y-2">
                                @foreach($this->overdueInvoicesList as $invoice)
                                    <div class="flex items-center justify-between bg-red-50/50 rounded-lg px-3 py-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-medium text-cashdash-text">#{{ $invoice['document_number'] }}</span>
                                            <span class="text-sm text-cashdash-muted">{{ $invoice['customer_name'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="text-sm font-semibold text-cashdash-text">{{ number_format($invoice['total'], 0, ',', ' ') }} kr</span>
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                                                {{ $invoice['days_overdue'] }} dagar försenad
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Charts Row --}}
                <div class="grid lg:grid-cols-3 gap-4 md:gap-6">
                    {{-- Main Cash Flow Chart --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-semibold text-cashdash-text text-lg">Kassaflödesprognos</h3>
                                <p class="text-cashdash-muted text-sm">Baserat på historik och öppna fakturor</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="setChartPeriod('12')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $chartPeriod === '12' ? 'bg-cashdash-forest text-white' : 'bg-forest-50 text-cashdash-forest hover:bg-forest-100' }}"
                                >
                                    12 mån
                                </button>
                                <button
                                    wire:click="setChartPeriod('6')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $chartPeriod === '6' ? 'bg-cashdash-forest text-white' : 'bg-forest-50 text-cashdash-forest hover:bg-forest-100' }}"
                                >
                                    6 mån
                                </button>
                                <button
                                    wire:click="setChartPeriod('3')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $chartPeriod === '3' ? 'bg-cashdash-forest text-white' : 'bg-forest-50 text-cashdash-forest hover:bg-forest-100' }}"
                                >
                                    3 mån
                                </button>
                            </div>
                        </div>
                        <div id="cashflow-chart" class="h-64 md:h-72" wire:ignore></div>
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

                    {{-- Runway Radial Chart --}}
                    <div class="bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card flex flex-col">
                        <h3 class="font-semibold text-cashdash-text text-lg mb-1">Runway-status</h3>
                        <p class="text-cashdash-muted text-sm mb-2">Hur länge räcker pengarna?</p>
                        <div id="runway-radial-chart" class="h-36 flex-shrink-0 -mt-2" wire:ignore></div>
                        <div class="mt-8 pt-4 border-t border-forest-100 space-y-2">
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
                                <span class="text-cashdash-success font-medium">&gt; 60 dagar {{ $snapshot->runway_days > 60 ? '✓' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Row --}}
                <div class="grid lg:grid-cols-2 gap-4 md:gap-6">
                    {{-- AI Insights --}}
                    <div class="bg-gradient-to-br from-cashdash-forest/5 to-cashdash-gold/5 rounded-2xl p-5 md:p-6 border border-cashdash-forest/20">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cashdash-forest to-forest-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-cashdash-text">AI-drivna insikter</h3>
                                <p class="text-cashdash-muted text-xs">Uppdaterad {{ $lastUpdated ?? 'nyss' }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @forelse($snapshot->insights ?? [] as $insight)
                                @php
                                    $borderColor = match($insight['type'] ?? 'info') {
                                        'success' => 'border-cashdash-success/20',
                                        'warning' => 'border-yellow-500/20',
                                        'danger' => 'border-red-500/20',
                                        default => 'border-cashdash-forest/20',
                                    };
                                    $iconBg = match($insight['type'] ?? 'info') {
                                        'success' => 'bg-cashdash-success/20',
                                        'warning' => 'bg-yellow-500/20',
                                        'danger' => 'bg-red-500/20',
                                        default => 'bg-cashdash-forest/20',
                                    };
                                    $iconColor = match($insight['type'] ?? 'info') {
                                        'success' => 'text-cashdash-success',
                                        'warning' => 'text-yellow-600',
                                        'danger' => 'text-red-500',
                                        default => 'text-cashdash-forest',
                                    };
                                @endphp
                                <div class="flex items-start gap-3 bg-white/60 rounded-xl p-3 border {{ $borderColor }}">
                                    <div class="w-6 h-6 rounded-full {{ $iconBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                        @if($insight['type'] === 'success')
                                            <svg class="w-3 h-3 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($insight['type'] === 'warning')
                                            <svg class="w-3 h-3 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-sm text-cashdash-text">
                                        @if(isset($insight['icon']))
                                            <span class="mr-1">{{ $insight['icon'] }}</span>
                                        @endif
                                        {{ $insight['text'] }}
                                    </p>
                                </div>
                            @empty
                                <div class="flex items-start gap-3 bg-white/60 rounded-xl p-3 border border-cashdash-success/20">
                                    <div class="w-6 h-6 rounded-full bg-cashdash-success/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-3 h-3 text-cashdash-success" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-cashdash-text"><strong>Allt ser bra ut!</strong> Inga varningar just nu.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Payment Patterns Chart --}}
                    <div class="bg-white rounded-2xl p-5 md:p-6 border border-forest-100 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-cashdash-text text-lg">Betalningsbeteende</h3>
                                <p class="text-cashdash-muted text-sm">Genomsnittlig betalningstid per kund</p>
                            </div>
                        </div>
                        <div id="payment-patterns-chart" class="h-48" wire:ignore></div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardCharts();

    // Re-initialize charts when Livewire updates
    Livewire.on('charts-updated', () => {
        setTimeout(() => {
            initializeDashboardCharts();
        }, 100);
    });
});

function initializeDashboardCharts() {
    // Destroy existing charts if they exist
    if (window.cashflowChart) {
        window.cashflowChart.destroy();
    }
    if (window.runwayRadialChart) {
        window.runwayRadialChart.destroy();
    }
    if (window.paymentPatternsChart) {
        window.paymentPatternsChart.destroy();
    }
    if (window.runwaySparkline) {
        window.runwaySparkline.destroy();
    }

    // Check if chart containers exist
    const cashflowContainer = document.querySelector("#cashflow-chart");
    const runwayRadialContainer = document.querySelector("#runway-radial-chart");
    const paymentPatternsContainer = document.querySelector("#payment-patterns-chart");
    const runwaySparklineContainer = document.querySelector("#runway-sparkline");

    @if($snapshot)
    const chartData = @json($this->cashFlowChartData);
    const runwayDays = {{ $snapshot->runway_days }};
    const runwayPercentage = {{ $this->runwayPercentage }};
    const paymentPatterns = @json($this->paymentPatternsData);

    // Main Cash Flow Area Chart
    if (cashflowContainer) {
        const cashflowOptions = {
            series: [{
                name: 'Faktiskt',
                type: 'area',
                data: chartData.actual
            }, {
                name: 'Prognos',
                type: 'line',
                data: chartData.projected
            }, {
                name: 'Min',
                type: 'area',
                data: chartData.min
            }, {
                name: 'Max',
                type: 'area',
                data: chartData.max
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
                categories: chartData.months,
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

        window.cashflowChart = new ApexCharts(cashflowContainer, cashflowOptions);
        window.cashflowChart.render();
    }

    // Runway Radial Chart
    if (runwayRadialContainer) {
        const runwayRadialOptions = {
            series: [runwayPercentage],
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
                            fontSize: '12px',
                            color: '#6B6B6B',
                            offsetY: 15
                        },
                        value: {
                            offsetY: -10,
                            fontSize: '28px',
                            fontWeight: 'bold',
                            color: '#1A3D2E',
                            formatter: function() { return runwayDays; }
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

        window.runwayRadialChart = new ApexCharts(runwayRadialContainer, runwayRadialOptions);
        window.runwayRadialChart.render();
    }

    // Payment Patterns Bar Chart
    if (paymentPatternsContainer) {
        const patternNames = paymentPatterns.map(p => p.name);
        const patternDays = paymentPatterns.map(p => p.days);
        const patternColors = patternDays.map(days => {
            if (days <= 5) return '#2D7A4F';
            if (days <= 10) return '#C4A962';
            if (days <= 14) return '#D97706';
            return '#DC2626';
        });

        const paymentPatternsOptions = {
            series: [{
                name: 'Dagar',
                data: patternDays
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
            colors: patternColors,
            dataLabels: {
                enabled: true,
                formatter: (val) => val + ' dgr',
                offsetX: 30,
                style: { fontSize: '11px', colors: ['#6B6B6B'] }
            },
            xaxis: {
                categories: patternNames,
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

        window.paymentPatternsChart = new ApexCharts(paymentPatternsContainer, paymentPatternsOptions);
        window.paymentPatternsChart.render();
    }

    // Runway Sparkline
    if (runwaySparklineContainer) {
        const sparklineOptions = {
            series: [{
                data: [65, 70, 72, 75, 78, 80, 82, runwayDays]
            }],
            chart: {
                type: 'line',
                height: '100%',
                sparkline: { enabled: true },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 1500
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            colors: ['#C4A962'],
            tooltip: { enabled: false }
        };

        window.runwaySparkline = new ApexCharts(runwaySparklineContainer, sparklineOptions);
        window.runwaySparkline.render();
    }
    @endif
}
</script>
@endpush
