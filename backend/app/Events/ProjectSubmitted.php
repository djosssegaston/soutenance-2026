<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Project $project) {}

    public function broadcastOn(): array
    {
        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();

        return array_map(fn ($id) => new PrivateChannel('notifications.'.$id), $adminIds);
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'project_submitted',
            'title' => 'Nouveau projet soumis',
            'message' => $this->project->titre.' par '.($this->project->owner?->name ?? ''),
            'project_id' => $this->project->id,
            'montant' => $this->project->montant_demande,
        ];
    }
}
