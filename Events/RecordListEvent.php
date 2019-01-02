<?php

namespace Modules\Imonitor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Modules\Imonitor\Entities\Record;


class RecordListEvent implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $newRecord;

    public function __construct(Record $newRecord)
    {
        $this->newRecord=$newRecord;
    }
    public function broadcastWith()
    {
        return [
            $this->newRecord
        ];
    }

    public function broadcastAs()
    {
        return 'newRecord';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('record-'.$this->newRecord->product->id);
    }
}
