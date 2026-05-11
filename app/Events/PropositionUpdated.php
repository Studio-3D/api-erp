<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropositionUpdated implements ShouldBroadcast
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

    public function broadcastAs()
    {
        return 'PropositionUpdated';
    }

    public function broadcastWith()
    {
        return [
            'bienId' => $this->bienId,
            'userId' => $this->userId,
        ];
    }
}