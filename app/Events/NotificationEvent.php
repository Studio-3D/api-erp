<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
class NotificationEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $NotificationId;

    public function __construct($NotificationId)
    {
        $this->NotificationId = $NotificationId;
        $this->broadcastVia('pusher_3');

        // Optional: Add logging for debugging
        \Log::info('NotificationEvent constructed', [
            'NotificationId' => $NotificationId
        ]);
    }

    /**
     * Channel diffusé
     */
    public function broadcastOn(): Channel
    {
        \Log::info('NotificationEvent broadcastOn called', [
            'channel' => 'Notifications',
            'NotificationId' => $this->NotificationId
        ]);

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
     *
     * Données envoyées au frontend
     */
    public function broadcastWith(): array
    {
        return [
            'NotificationId' => $this->NotificationId,
            'timestamp' => now()->toISOString()  // Added timestamp for better tracking
        ];
    }

}
