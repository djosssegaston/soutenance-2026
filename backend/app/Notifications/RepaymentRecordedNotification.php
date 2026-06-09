<?php

namespace App\Notifications;

use App\Models\Repayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepaymentRecordedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Repayment $repayment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $projectName = $this->repayment->project?->titre ?? 'Projet';

        return (new MailMessage)
            ->subject('Remboursement enregistre')
            ->line('Un remboursement a ete enregistre pour le projet: '.$projectName.'.')
            ->line('Montant: '.$this->repayment->montant.' FCFA')
            ->line('Merci de consulter la plateforme pour le detail.');
    }
}
