<?php

namespace App\Events;

use App\Models\Funding;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FundingReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Funding $funding) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->funding->project->porteur_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'funding',
            'title' => 'Financement reçu',
            'message' => number_format($this->funding->montant).' FCFA pour '.($this->funding->project?->titre ?? 'N/A'),
            'funding_id' => $this->funding->id,
            'project_id' => $this->funding->project_id,
            'montant' => $this->funding->montant,
        ];
    }
}
