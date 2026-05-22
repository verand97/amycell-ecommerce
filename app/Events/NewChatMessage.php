<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatMessage $message,
        public ChatSession $session
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->session->id),
            new PrivateChannel('admin.chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id'   => $this->message->id,
            'session_id'   => $this->session->id,
            'sender_id'    => $this->message->sender_id,
            'sender_name'  => $this->message->sender->name,
            'sender_type'  => $this->message->sender_type,
            'message'      => $this->message->message,
            'attachment'   => $this->message->attachment_url,
            'created_at'   => $this->message->created_at->toISOString(),
            'customer_name'=> $this->session->customer->name,
        ];
    }
}
