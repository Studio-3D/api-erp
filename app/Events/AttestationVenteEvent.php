<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttestationVenteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $reservationId;

    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;

        $this->broadcastVia('pusher_9');
    }

    public function broadcastOn()
    {
        return new Channel('attestation-vente-updates-' . $this->reservationId);
    }

    public function broadcastAs()
    {
        return 'AttestationVenteEvent';
    }

    public function broadcastWith()
    {
        return [
            'reservationId' => $this->reservationId,
            'timestamp' => now()->toISOString(),
        ];
    }
}