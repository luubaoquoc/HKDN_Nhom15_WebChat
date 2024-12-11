<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RoomMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chatData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chatData)
    {
        //
        $this->chatData = $chatData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastWith(){
        Log::info('Data being broadcasted: ', ['chat' => $this->chatData]);
        return ['chat' => $this->chatData];
    }

    public function broadcastAs(){
       return 'getRoomChatMessage';
    }
    
    public function broadcastOn()
    {
        Log::info('Broadcasting RoomMessageEvent for room: ' . $this->chatData->room_id);
        Log::info('Message content: ' . $this->chatData->content);
        return new PrivateChannel('broadcast-group-message.' . $this->chatData->room_id);
    }
}