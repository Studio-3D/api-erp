<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvancesEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $reservationId;
    public $userId;

    public function __construct($reservationId, $userId = null)
    {
        $this->reservationId = $reservationId;
        $this->userId = $userId;

        $this->broadcastVia('pusher_7');
    }

    public function broadcastOn()
    {
        if ($this->userId) {
            return new Channel("res-show-user-{$this->userId}");
        }

        return new Channel("avances-updates-{$this->reservationId}");
    }

    public function broadcastAs()
    {
        return 'AvancesEvent';
    }

    
    public function broadcastWith()
    {
        return [
            'reservationId' => $this->reservationId,
            'userId' => $this->userId,
            'timestamp' => now()->toISOString(),
        ];
    }
}