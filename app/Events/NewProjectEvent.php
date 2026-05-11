<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewProjectEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $projetData;

    public function __construct($projetData)
    {
        $this->projetData = $projetData;


        // connexion Pusher par défaut
        $this->broadcastVia('pusher');

        // Optional: Add logging for debugging
        \Log::info('NewProjectEvent constructed', [
            'projetData' => $projetData
        ]);

    }

    public function broadcastOn()
    {
        \Log::info('NewProjectEvent broadcastOn called', [
            'channel' => 'projets'
        ]);

        return new Channel('projets');
    }

<<<<<<< HEAD
=======
    // Optional but recommended: Add broadcastAs method
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastAs()
    {
        return 'NewProjectEvent';
    }

<<<<<<< HEAD
=======
    // Optional: Add data to broadcast
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastWith()
    {
        return [
            'projetData' => $this->projetData,
<<<<<<< HEAD
        ];
    }
}
=======
            'timestamp' => now()->toISOString()
        ];
    }
}
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
