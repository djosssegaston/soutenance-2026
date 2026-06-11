<?php

namespace App\Events;

use App\Models\Repayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepaymentRecorded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Repayment $repayment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->repayment->project->porteur_id),
            new PrivateChannel('notifications.'.$this->repayment->institution_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'repayment',
            'title' => 'Remboursement '.($this->repayment->statut === 'paye' ? 'reçu' : 'en attente'),
            'message' => 'Projet: '.($this->repayment->project?->titre ?? ''),
            'repayment_id' => $this->repayment->id,
            'montant' => $this->repayment->montant_total,
            'statut' => $this->repayment->statut,
        ];
    }
}
