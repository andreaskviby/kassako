<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    @if (!$codeSent)
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Skapa konto</h2>
            <p class="text-sm text-gray-600">Fyll i dina uppgifter så skickar vi en verifieringskod till din e-post.</p>
        </div>

        <form wire:submit="sendCode">
            <div>
                <x-label for="name" value="Namn" />
                <x-input
                    id="name"
                    class="block mt-1 w-full"
                    type="text"
                    wire:model="name"
                    required
                    autofocus
                    autocomplete="name"
                />
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <x-label for="email" value="E-postadress" />
                <x-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    wire:model="email"
                    required
                    autocomplete="username"
                />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('login') }}">
                    Har du redan ett konto?
                </a>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Skicka kod</span>
                    <span wire:loading>Skickar...</span>
                </x-button>
            </div>
        </form>
    @else
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Verifiera din e-post</h2>
            <p class="text-sm text-gray-600">
                Vi har skickat en 6-siffrig kod till <strong>{{ $email }}</strong>
            </p>
        </div>

        <form wire:submit="verify">
            <div>
                <x-label for="code" value="Verifieringskod" />
                <x-input
                    id="code"
                    class="block mt-1 w-full text-center text-2xl tracking-widest font-mono"
                    type="text"
                    wire:model="code"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                    autofocus
                    placeholder="000000"
                />
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <button
                    type="button"
                    wire:click="resendCode"
                    wire:loading.attr="disabled"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Skicka ny kod
                </button>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Skapa konto</span>
                    <span wire:loading>Skapar...</span>
                </x-button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-gray-200">
            <button
                type="button"
                wire:click="$set('codeSent', false)"
                class="text-sm text-gray-500 hover:text-gray-700"
            >
                ← Ändra uppgifter
            </button>
        </div>
    @endif
</x-authentication-card>
