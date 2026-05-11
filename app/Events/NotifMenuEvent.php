<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifMenuEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $NotifMenuId;

    public function __construct($NotifMenuId)
    {
        $this->NotifMenuId = $NotifMenuId;

        $this->broadcastVia('pusher_5');
    }

    public function broadcastOn()
    {
        return new Channel('NotifMenu');
    }

    public function broadcastAs()
    {
        return 'NotifMenuEvent';
    }

    public function broadcastWith()
    {
        return [
            'NotifMenuId' => $this->NotifMenuId,
        ];
    }
}