<?php

namespace Modules\Imonitor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Modules\Imonitor\Entities\Product;


class ProductListEvent implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $product;

    public function __construct(Product $product)
    {
        $this->product=$product;
    }
    public function broadcastWith()
    {
        return [
            $this->product
        ];
    }

    public function broadcastAs()
    {
        return 'product';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('product-'.$this->product->id);
    }
}
