<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $NotificationId;

    public function __construct($NotificationId)
    {
        $this->NotificationId = $NotificationId;


        // optionnel car déjà connexion par défaut
        $this->broadcastVia('pusher');

        // Optional: Add logging for debugging
        \Log::info('NotificationEvent constructed', [
            'NotificationId' => $NotificationId
        ]);

    }

    public function broadcastOn(): Channel
    {
        \Log::info('NotificationEvent broadcastOn called', [
            'channel' => 'Notifications',
            'NotificationId' => $this->NotificationId
        ]);

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
            'timestamp' => now()->toISOString()  // Added timestamp for better tracking
        ];
    }
}
