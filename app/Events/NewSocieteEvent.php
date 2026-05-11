<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSocieteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $societeData;

    public function __construct($societeData)
    {
        $this->societeData = $societeData;

        // utilise la connexion Pusher par défaut
        $this->broadcastVia('pusher');
    }

    public function broadcastOn()
    {
        return new Channel('societes');
    }

    public function broadcastAs()
    {
        return 'NewSocieteEvent';
    }

    public function broadcastWith()
    {
        return [
            'societeData' => $this->societeData,
        ];
    }
}