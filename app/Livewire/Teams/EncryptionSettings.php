<?php

namespace App\Livewire\Teams;

use App\Services\Encryption\TeamEncryptionService;
use Livewire\Component;

class EncryptionSettings extends Component
{
    public $team;
    public bool $hasEncryption = false;
    public bool $isUnlocked = false;
    public bool $showDownloadModal = false;
    public string $passphrase = '';

    public function mount($team): void
    {
        $this->team = $team;
        $this->hasEncryption = $this->team->hasEncryptionInitialized();

        if ($this->hasEncryption) {
            $encryptionService = app(TeamEncryptionService::class);
            $sessionId = session('encryption_session_id');
            $this->isUnlocked = $sessionId && $encryptionService->isUnlocked($this->team, $sessionId);
        }
    }

    public function openDownloadModal(): void
    {
        $this->showDownloadModal = true;
        $this->passphrase = '';
    }

    public function closeDownloadModal(): void
    {
        $this->showDownloadModal = false;
        $this->passphrase = '';
    }

    public function downloadRecoveryPdf(): void
    {
        $this->validate([
            'passphrase' => ['required', 'string', 'min:12'],
        ]);

        // Verify the passphrase is correct by trying to unlock
        $encryptionService = app(TeamEncryptionService::class);
        $testSessionId = 'verify-' . uniqid();

        $isValid = $encryptionService->unlockEncryption($this->team, $this->passphrase, $testSessionId);

        if (!$isValid) {
            $this->addError('passphrase', 'Felaktig lösenfras. Försök igen.');
            return;
        }

        // Lock the test session
        $encryptionService->lockEncryption($this->team, $testSessionId);

        // Redirect to download with passphrase in session
        session(['recovery_passphrase' => $this->passphrase]);

        $this->redirect(route('encryption.download-recovery-verified'));
    }

    public function render()
    {
        return view('livewire.teams.encryption-settings');
    }
}
