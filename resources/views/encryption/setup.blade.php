<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Secure Your Data</h2>
            <p class="mt-2 text-sm text-gray-600">
                Set up encryption for {{ $team->name }}
            </p>
        </div>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Zero-Knowledge Encryption
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Your financial data will be encrypted with a passphrase that only you know. We cannot access or recover your data without this passphrase.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Important: Save Your Passphrase
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>If you forget your passphrase, your data cannot be recovered. We recommend storing it in a password manager.</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('encryption.setup.store') }}">
            @csrf

            <div class="mt-4">
                <x-label for="passphrase" value="{{ __('Encryption Passphrase') }}" />
                <x-input id="passphrase"
                         class="block mt-1 w-full"
                         type="password"
                         name="passphrase"
                         required
                         autocomplete="new-password"
                         minlength="12"
                         placeholder="At least 12 characters" />
                <p class="mt-1 text-xs text-gray-500">
                    Must include uppercase, lowercase, and numbers
                </p>
            </div>

            <div class="mt-4">
                <x-label for="passphrase_confirmation" value="{{ __('Confirm Passphrase') }}" />
                <x-input id="passphrase_confirmation"
                         class="block mt-1 w-full"
                         type="password"
                         name="passphrase_confirmation"
                         required
                         autocomplete="new-password"
                         minlength="12" />
            </div>

            @error('passphrase')
                <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            <div class="flex items-center justify-end mt-6">
                <x-button class="w-full justify-center">
                    {{ __('Enable Encryption') }}
                </x-button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                How It Works
            </h4>
            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <span class="flex-shrink-0 h-5 w-5 text-green-500">1.</span>
                    <span class="ml-2">Your passphrase creates an encryption key</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 h-5 w-5 text-green-500">2.</span>
                    <span class="ml-2">All financial data is encrypted before storage</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 h-5 w-5 text-green-500">3.</span>
                    <span class="ml-2">Only you can decrypt your data with your passphrase</span>
                </li>
            </ul>
        </div>
    </x-authentication-card>
</x-guest-layout>
