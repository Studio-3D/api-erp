<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
<<<<<<< HEAD
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
=======
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS LINE
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifMenuEvent implements ShouldBroadcastNow  // CHANGE THIS INTERFACE
{
<<<<<<< HEAD
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;
=======
    use Dispatchable, InteractsWithSockets, SerializesModels;
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320

    public $NotifMenuId;

    public function __construct($NotifMenuId)
    {
        $this->NotifMenuId = $NotifMenuId;


        $this->broadcastVia('pusher_5');

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

<<<<<<< HEAD
=======
    // Optional but recommended: Add broadcastAs method
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastAs()
    {
        return 'NotifMenuEvent';
    }

<<<<<<< HEAD
=======
    // Optional: Add data to broadcast
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
    public function broadcastWith()
    {
        return [
            'NotifMenuId' => $this->NotifMenuId,
<<<<<<< HEAD
        ];
    }
}
=======
            'timestamp' => now()->toDateTimeString()
        ];
    }
}
>>>>>>> 8fb3d2b7e82dc4c416c603bc70ffa3b4bb6d1320
