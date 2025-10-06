<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(public User $receiver, public User $sender, public Conversation $conversation, public string $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel("chat.{$this->receiver->id}"); // Public chat channel
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
