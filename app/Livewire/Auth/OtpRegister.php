<?php

namespace App\Livewire\Auth;

use App\Models\Team;
use App\Models\User;
use App\Notifications\OtpCodeNotification;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class OtpRegister extends Component
{
    public string $name = '';
    public string $email = '';
    public string $code = '';
    public bool $codeSent = false;

    protected function rules(): array
    {
        if (! $this->codeSent) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
            ];
        }

        return [
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Namn krävs.',
            'email.required' => 'E-postadress krävs.',
            'email.email' => 'Ange en giltig e-postadress.',
            'email.unique' => 'Denna e-postadress är redan registrerad.',
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

        $code = $otpService->createOtp($this->email, 'registration');

        Notification::route('mail', $this->email)
            ->notify(new OtpCodeNotification($code, 'registration'));

        $otpService->incrementRequestRateLimit($this->email);

        $this->codeSent = true;
        session()->flash('status', 'Verifieringskod skickad till din e-post.');
    }

    public function verify(OtpService $otpService): void
    {
        $this->validate();

        $result = $otpService->verifyOtp($this->email, $this->code, 'registration');

        if (! $result->isSuccess()) {
            $this->addError('code', $result->getMessage());
            return;
        }

        $user = DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);

            $this->createTeam($user);

            return $user;
        });

        session()->regenerate();
        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function resendCode(OtpService $otpService): void
    {
        $this->code = '';
        $this->codeSent = false;
        $this->sendCode($otpService);
    }

    private function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . 's företag',
            'personal_team' => true,
        ]));

        $user->switchTeam($user->ownedTeams()->first());
    }

    public function render()
    {
        return view('livewire.auth.otp-register');
    }
}
