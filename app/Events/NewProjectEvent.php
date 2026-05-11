<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewProjectEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $projetData;

    public function __construct($projetData)
    {
        $this->projetData = $projetData;

        // connexion Pusher par défaut
        $this->broadcastVia('pusher');
    }

    public function broadcastOn()
    {
        return new Channel('projets');
    }

    public function broadcastAs()
    {
        return 'NewProjectEvent';
    }

    public function broadcastWith()
    {
        return [
            'projetData' => $this->projetData,
        ];
    }
}