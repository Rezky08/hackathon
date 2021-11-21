<?php

namespace App\Events\Sayembara;

use App\Models\Sayembara\Winner;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExistingParticipantWin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Winner
     */
    public Winner $winner;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Winner $winner)
    {
        $this->winner = $winner;
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
