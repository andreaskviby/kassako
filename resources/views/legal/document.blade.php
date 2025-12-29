<x-guest-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <a href="/" class="flex items-center gap-2">
                    <img src="/favicon.svg" alt="CashDash" class="w-8 h-8">
                    <span class="font-bold text-xl text-[#1A3D2E]">Cash<span class="text-[#C4A962]">Dash</span></span>
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-4xl mx-auto px-4 py-12">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 md:p-12">
                <article class="prose prose-lg max-w-none prose-headings:text-[#1A3D2E] prose-a:text-[#1A3D2E] prose-strong:text-gray-900">
                    {!! $content !!}
                </article>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Stafe Development AB. Alla rättigheter förbehållna.</p>
        </footer>
    </div>
</x-guest-layout>
