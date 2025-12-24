<?php

namespace App\Livewire\Teams;

use App\Services\Encryption\TeamEncryptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FortnoxConnectionManager extends Component
{
    public $team;
    public bool $hasEncryption = false;
    public bool $isEncryptionUnlocked = false;
    public bool $hasFortnox = false;
    public ?string $syncStatus = null;
    public ?string $lastSyncAt = null;

    public function mount($team): void
    {
        $this->team = $team;
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $this->hasEncryption = $this->team->hasEncryptionInitialized();
        $this->hasFortnox = $this->team->hasFortnoxConnected();

        if ($this->hasEncryption) {
            $encryptionService = app(TeamEncryptionService::class);
            $sessionId = session('encryption_session_id');
            $this->isEncryptionUnlocked = $sessionId && $encryptionService->isUnlocked($this->team, $sessionId);
        }

        if ($this->hasFortnox) {
            $connection = $this->team->fortnoxConnection;
            $this->syncStatus = $connection?->sync_status;
            $this->lastSyncAt = $connection?->last_sync_at?->locale('sv')->diffForHumans();
        }
    }

    public function disconnect(): void
    {
        $connection = $this->team->fortnoxConnection;

        if ($connection) {
            $connection->update([
                'is_active' => false,
                'access_token' => null,
                'refresh_token' => null,
            ]);
        }

        $this->hasFortnox = false;
        $this->syncStatus = null;
        $this->lastSyncAt = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Fortnox-kopplingen är borttagen.',
        ]);
    }

    public function render()
    {
        return view('livewire.teams.fortnox-connection-manager');
    }
}
