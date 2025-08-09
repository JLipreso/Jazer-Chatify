<?php

namespace Jazer\Chatify\Http\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project_refid;
    public $convo_refid;
    public $message;
    public $user_refid;

    public function __construct($project_refid = null, $convo_refid = null, $message, $user_refid = null)
    {
        $this->project_refid        = $project_refid;
        $this->convo_refid          = $convo_refid;
        $this->message              = $message;
        $this->user_refid           = $user_refid;
    }

    public function broadcastOn()
    {
        return new Channel('chat.conversation.' . $this->convo_refid);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        $data = [
            'project_refid'     => $this->project_refid,
            'convo_refid'       => $this->convo_refid,
            'message'           => $this->message,
            'user_refid'        => $this->user_refid,
            'timestamp'         => now()->toDateTimeString(),
            'random_id'         => uniqid()
        ];
        return $data;
    }
    
    public function broadcastWhen()
    {
        return true;
    }
}
