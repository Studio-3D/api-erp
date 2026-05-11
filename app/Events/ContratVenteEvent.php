<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContratVenteEvent implements ShouldBroadcastNow  // CHANGE THIS
{
   use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $reservationId;

    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;

         $this->broadcastVia('pusher_10');


        // Remove the config line below - not needed with broadcastConnection()
        // config(['broadcasting.default' => 'pusher_10']);

        // Optional: Add logging for debugging
        \Log::info('ContratVenteEvent constructed', [
            'reservationId' => $reservationId,
            'connection' => 'pusher_10'
        ]);
    }

    public function broadcastOn()
    {
        \Log::info('ContratVenteEvent broadcastOn called', [
            'reservationId' => $this->reservationId,
            'channel' => 'contrat-vente-updates-' . $this->reservationId
        ]);

        // Broadcast to reservation-specific channel
        return new Channel('contrat-vente-updates-' . $this->reservationId);
    }


    public function broadcastAs()
    {
        return 'ContratVenteEvent';
    }

    public function broadcastWith()
    {
        return [
            'reservationId' => $this->reservationId,
            'timestamp' => now()->toISOString()
        ];
    }
}
