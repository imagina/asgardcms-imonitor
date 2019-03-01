<?php

namespace Modules\Imonitor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Modules\Imonitor\Entities\Event;


class EventListEvent implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $newEvent;

    public function __construct(Event $newEvent)
    {
        $this->newEvent=$newEvent;
    }
    public function broadcastWith()
    {
        return [
            $this->newEvent
        ];
    }

    public function broadcastAs()
    {
        return 'newEvent';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('event-'.$this->newEvent->product->id);

    }
}
