<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifMenuEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $NotifMenuId;

    public function __construct($NotifMenuId)
    {
        $this->NotifMenuId = $NotifMenuId;

        // Optional: Add logging for debugging
        \Log::info('NotifMenuEvent constructed', [
            'NotifMenuId' => $NotifMenuId
        ]);
    }

    public function broadcastOn()
    {
        \Log::info('NotifMenuEvent broadcastOn called', [
            'channel' => 'NotifMenu'
        ]);

        return new Channel('NotifMenu');
    }

    // Optional but recommended: Add broadcastAs method
    public function broadcastAs()
    {
        return 'NotifMenuEvent';
    }

    // Optional: Add data to broadcast
    public function broadcastWith()
    {
        return [
            'NotifMenuId' => $this->NotifMenuId,
            'timestamp' => now()->toISOString()
        ];
    }
}
