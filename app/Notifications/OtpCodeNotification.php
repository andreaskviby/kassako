<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $code,
        private string $purpose = 'login'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjects = [
            'login' => "Din inloggningskod: {$this->code}",
            'registration' => "Din registreringskod: {$this->code}",
            'password_reset' => "Din återställningskod: {$this->code}",
        ];

        $subject = $subjects[$this->purpose] ?? "Din verifieringskod: {$this->code}";

        $greetings = [
            'login' => 'Välkommen tillbaka!',
            'registration' => 'Välkommen till CashDash!',
            'password_reset' => 'Återställ ditt lösenord',
        ];

        $greeting = $greetings[$this->purpose] ?? 'Hej!';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line('Använd koden nedan för att verifiera din identitet:')
            ->line('')
            ->line("**{$this->code}**")
            ->line('')
            ->line('Koden är giltig i 10 minuter.')
            ->line('')
            ->line('Om du inte begärt denna kod kan du ignorera detta meddelande.')
            ->salutation('Vänliga hälsningar, CashDash');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'purpose' => $this->purpose,
        ];
    }
}
