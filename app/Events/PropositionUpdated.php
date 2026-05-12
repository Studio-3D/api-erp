<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;

use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;  // CHANGE THIS LINE

use Illuminate\Queue\SerializesModels;

class PropositionUpdated implements ShouldBroadcastNow  // CHANGE THIS INTERFACE
{
    use Dispatchable, InteractsWithSockets, InteractsWithBroadcasting, SerializesModels;

    public $bienId;
    public $userId;

    public function __construct($bienId, $userId)
    {
        $this->bienId = $bienId;
        $this->userId = $userId;

        $this->broadcastVia('pusher_4');

        \Log::info('PropositionUpdated event constructed', [
            'bienId' => $bienId,
            'userId' => $userId
        ]);

    }

    public function broadcastOn()
    {
        \Log::info('PropositionUpdated broadcastOn called', [
            'channel' => 'proposition-updates'
        ]);

        return new Channel('proposition-updates');
    }



}

