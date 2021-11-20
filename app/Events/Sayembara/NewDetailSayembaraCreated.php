<?php

namespace App\Events\Sayembara;

use App\Models\Sayembara\Detail;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDetailSayembaraCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Detail
     */
    public Detail $sayembaraDetail;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Detail $sayembaraDetail)
    {
        $this->sayembaraDetail = $sayembaraDetail;
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
