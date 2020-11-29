<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusMessageHandled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $receiverName;
    private $message;

    public function __construct(string $receiverName, $message)
    {
        $this->receiverName = $receiverName;
        $this->message = $message;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function getMessage()
    {
        return $this->message;
    }



    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
