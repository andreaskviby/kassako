<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-6 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Unlock Your Data</h2>
            <p class="mt-2 text-sm text-gray-600">
                Enter your passphrase to access {{ $team->name }}
            </p>
        </div>

        @if(session('warning'))
            <div class="mb-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-700">{{ session('info') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('encryption.unlock.store') }}">
            @csrf

            <div>
                <x-label for="passphrase" value="{{ __('Encryption Passphrase') }}" />
                <x-input id="passphrase"
                         class="block mt-1 w-full"
                         type="password"
                         name="passphrase"
                         required
                         autofocus
                         autocomplete="current-password" />
            </div>

            @error('passphrase')
                <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            <div class="flex items-center justify-end mt-6">
                <x-button class="w-full justify-center">
                    {{ __('Unlock') }}
                </x-button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Your session will automatically lock after 30 minutes of inactivity.
            </p>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">
                Forgot your passphrase? Unfortunately, encrypted data cannot be recovered without the original passphrase.
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
