<?php

namespace Modules\Imonitor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Modules\Imonitor\Entities\Record;

class VariblesListEvent
{
    use SerializesModels, InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $record;

    public function __construct(Record $record)
    {
        $this->record=$record;
    }
    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            $this->record
        ];
    }

    public function broadcastAs()
    {
        return 'record';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('register-'.$this->record->product->id);
    }
}
