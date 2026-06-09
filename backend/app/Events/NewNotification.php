<?php

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public UserNotification $notification) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->notification->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'notification',
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->content,
            'created_at' => $this->notification->created_at?->diffForHumans(),
        ];
    }
}
