<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Notifications\OtpCodeNotification;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class OtpLogin extends Component
{
    public string $email = '';
    public string $code = '';
    public bool $codeSent = false;
    public bool $remember = false;

    protected function rules(): array
    {
        if (! $this->codeSent) {
            return [
                'email' => ['required', 'email', 'exists:users,email'],
            ];
        }

        return [
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'E-postadress krävs.',
            'email.email' => 'Ange en giltig e-postadress.',
            'email.exists' => 'Inget konto hittat med denna e-post.',
            'code.required' => 'Verifieringskod krävs.',
            'code.size' => 'Koden måste vara 6 siffror.',
        ];
    }

    public function sendCode(OtpService $otpService): void
    {
        $this->validate();

        if ($otpService->isRequestRateLimited($this->email)) {
            $this->addError('email', 'För många försök. Vänta en stund.');
            return;
        }

        $user = User::where('email', $this->email)->first();
        $code = $otpService->createOtp($this->email, 'login', $user?->id);

        Notification::route('mail', $this->email)
            ->notify(new OtpCodeNotification($code, 'login'));

        $otpService->incrementRequestRateLimit($this->email);

        $this->codeSent = true;
        session()->flash('status', 'Verifieringskod skickad till din e-post.');
    }

    public function verify(OtpService $otpService): void
    {
        $this->validate();

        $result = $otpService->verifyOtp($this->email, $this->code, 'login');

        if (! $result->isSuccess()) {
            $this->addError('code', $result->getMessage());
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('code', 'Användare hittades inte.');
            return;
        }

        session()->regenerate();
        Auth::login($user, $this->remember);

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function resendCode(OtpService $otpService): void
    {
        $this->code = '';
        $this->codeSent = false;
        $this->sendCode($otpService);
    }

    public function render()
    {
        return view('livewire.auth.otp-login');
    }
}
