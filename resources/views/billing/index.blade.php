<x-app-layout>
    <div class="min-h-screen bg-[#FDFBF7] py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <h1 class="font-serif text-3xl text-[#1C1C1C] mb-8">Fakturering</h1>

            {{-- Current Plan --}}
            <div class="bg-white rounded-2xl border border-[#F5F0E8] p-6 mb-6">
                <h2 class="font-medium text-[#1C1C1C] mb-4">Din plan</h2>

                @if($onTrial && !$subscribed)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[#1C1C1C]">Provperiod</p>
                            <p class="text-sm text-[#6B6B6B]">
                                {{ $team->trial_ends_at->diffInDays(now()) }} dagar kvar
                            </p>
                        </div>
                        <span class="bg-[#C4A962] text-white text-sm px-3 py-1 rounded-full">
                            Gratis
                        </span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#F5F0E8]">
                        <a
                            href="{{ route('billing.subscribe') }}"
                            class="inline-flex items-center gap-2 bg-[#1A3D2E] text-white font-medium px-6 py-3 rounded-xl hover:bg-[#143024] transition-colors"
                        >
                            Uppgradera till 149 kr/mån
                        </a>
                    </div>

                @elseif($subscribed)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[#1C1C1C]">CashDash Pro</p>
                            <p class="text-sm text-[#6B6B6B]">149 kr/månad</p>
                        </div>
                        <span class="bg-[#2D7A4F] text-white text-sm px-3 py-1 rounded-full">
                            Aktiv
                        </span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#F5F0E8] flex gap-4">
                        <a
                            href="{{ route('billing.portal') }}"
                            class="text-[#1A3D2E] hover:underline text-sm"
                        >
                            Hantera betalning
                        </a>

                        @if($subscription && !$subscription->cancelled())
                            <form method="POST" action="{{ route('billing.cancel') }}">
                                @csrf
                                <button type="submit" class="text-[#DC2626] hover:underline text-sm">
                                    Avsluta prenumeration
                                </button>
                            </form>
                        @elseif($subscription && $subscription->cancelled())
                            <form method="POST" action="{{ route('billing.resume') }}">
                                @csrf
                                <button type="submit" class="text-[#2D7A4F] hover:underline text-sm">
                                    Återaktivera
                                </button>
                            </form>
                        @endif
                    </div>

                @else
                    <div class="text-center py-8">
                        <p class="text-[#6B6B6B] mb-4">Ingen aktiv prenumeration</p>
                        <a
                            href="{{ route('billing.subscribe') }}"
                            class="inline-flex items-center gap-2 bg-[#1A3D2E] text-white font-medium px-6 py-3 rounded-xl hover:bg-[#143024] transition-colors"
                        >
                            Starta 14 dagars gratis test
                        </a>
                    </div>
                @endif
            </div>

            {{-- Invoice History --}}
            @if($invoices && $invoices->isNotEmpty())
                <div class="bg-white rounded-2xl border border-[#F5F0E8] p-6">
                    <h2 class="font-medium text-[#1C1C1C] mb-4">Fakturor</h2>
                    <div class="space-y-3">
                        @foreach($invoices as $invoice)
                            <div class="flex items-center justify-between py-2">
                                <div>
                                    <p class="text-[#1C1C1C]">{{ $invoice->date()->format('Y-m-d') }}</p>
                                    <p class="text-sm text-[#6B6B6B]">{{ $invoice->total() }}</p>
                                </div>
                                <a
                                    href="{{ $invoice->invoicePdf() }}"
                                    class="text-[#1A3D2E] hover:underline text-sm"
                                >
                                    Ladda ner
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
