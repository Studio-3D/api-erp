<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvancesEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $reservationId;
    public $userId;

    public function __construct($reservationId, $userId = null)
    {
        $this->reservationId = $reservationId;
        $this->userId = $userId;

        $this->broadcastVia('pusher_7');

        // Remove the config line below - it's not needed with broadcastConnection()
        // config(['broadcasting.default' => 'pusher_7']);

        // Optional: Add logging for debugging
        \Log::info('AvancesEvent constructed', [
            'reservationId' => $reservationId,
            'userId' => $userId
        ]);

    }

    public function broadcastOn()
    {

        \Log::info('AvancesEvent broadcastOn called', [
            'reservationId' => $this->reservationId,
            'userId' => $this->userId
        ]);

        // Broadcast to reservation-specific channel
        if ($this->userId) {
            // User-specific channel

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