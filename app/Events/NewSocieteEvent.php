<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSocieteEvent implements ShouldBroadcastNow  // CHANGE THIS
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $societeData;

    public function __construct($societeData)
    {
        $this->societeData = $societeData;


        // utilise la connexion Pusher par défaut
        $this->broadcastVia('pusher');

        // Optional: Add logging for debugging
        \Log::info('NewSocieteEvent constructed', [
            'societeData' => $societeData
        ]);

    }

    public function broadcastOn()
    {
        \Log::info('NewSocieteEvent broadcastOn called', [
            'channel' => 'societes'
        ]);

        return new Channel('societes');
    }

<<<<<<< HEAD
=======
    // Optional but recommended: Add broadcastAs method
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastAs()
    {
        return 'NewSocieteEvent';
    }

<<<<<<< HEAD
=======
    // Optional: Add data to broadcast
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastWith()
    {
        return [
            'societeData' => $this->societeData,
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
