<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $NotificationId;

    public function __construct($NotificationId)
    {
        $this->NotificationId = $NotificationId;

        // optionnel car déjà connexion par défaut
        $this->broadcastVia('pusher');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('Notifications');
    }

    public function broadcastAs(): string
    {
        return 'NotificationEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'NotificationId' => $this->NotificationId,
        ];
    }
}