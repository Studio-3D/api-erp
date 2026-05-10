<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $NotificationId;

    public function __construct($NotificationId)
    {
        $this->NotificationId = $NotificationId;
    }

    /**
     * Channel diffusé
     */
    public function broadcastOn(): Channel
    {
        return new Channel('Notifications');
    }

    /**
     * Nom exact de l'event côté frontend
     */
    public function broadcastAs(): string
    {
        return 'NotificationEvent';
    }

    /**
     * Données envoyées au frontend
     */
    public function broadcastWith(): array
    {
        return [
            'NotificationId' => $this->NotificationId,
        ];
    }
}