<?php

namespace App\Events\Sayembara;

use App\Models\Sayembara;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExistingSayembaraUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sayembara $sayembara;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Sayembara $sayembara)
    {
        $this->sayembara = $sayembara;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
