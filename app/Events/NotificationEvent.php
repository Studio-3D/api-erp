<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $NotificationId;

    public function __construct($NotificationId)
    {
        $this->NotificationId = $NotificationId;

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
            'timestamp' => now()->toISOString()
        ];
    }

    // This is the CORRECT way to specify the connection
    public function broadcastConnection()
    {
        return 'pusher_notify';
    }
}
