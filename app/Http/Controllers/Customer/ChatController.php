<?php

namespace App\Http\Controllers\Customer;

use App\Events\ChatQueueUpdated;
use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function startSession(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:200',
        ]);

        // Check if customer already has active/waiting session
        $existing = ChatSession::where('customer_id', auth()->id())
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        if ($existing) {
            return response()->json([
                'session_id'     => $existing->id,
                'status'         => $existing->status,
                'queue_position' => $existing->queue_position,
                'message'        => 'Sesi chat Anda masih aktif.',
            ]);
        }

        $session = ChatSession::create([
            'customer_id'    => auth()->id(),
            'subject'        => $request->subject ?? 'Pertanyaan Umum',
            'status'         => 'waiting',
            'queue_position' => ChatSession::where('status', 'waiting')->count() + 1,
        ]);

        // Notify admin of new queue entry
        try {
            broadcast(new ChatQueueUpdated($this->getQueueData()));
        } catch (\Exception $e) {
            Log::warning('Broadcast chat queue gagal: ' . ($e->getMessage() ?? 'Unknown error'));
        }

        return response()->json([
            'session_id'     => $session->id,
            'status'         => 'waiting',
            'queue_position' => $session->queue_position,
            'message'        => 'Anda telah bergabung dalam antrian chat.',
        ]);
    }

    public function sendMessage(Request $request, ChatSession $session)
    {
        abort_if($session->customer_id !== auth()->id(), 403);
        abort_if($session->status === 'closed', 422);

        $request->validate([
            'message'    => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
        }

        $message = ChatMessage::create([
            'session_id'  => $session->id,
            'sender_id'   => auth()->id(),
            'sender_type' => 'customer',
            'message'     => $request->message,
            'attachment'  => $attachmentPath,
        ]);

        $session->increment('unread_count_admin');
        $message->load('sender');

        try {
            broadcast(new NewChatMessage($message, $session));
        } catch (\Exception $e) {
            Log::warning('Broadcast customer chat message gagal: ' . ($e->getMessage() ?? 'Unknown error'));
        }

        return response()->json([
            'success'    => true,
            'message_id' => $message->id,
            'created_at' => $message->created_at->toISOString(),
        ]);
    }

    public function getMessages(ChatSession $session)
    {
        abort_if($session->customer_id !== auth()->id(), 403);

        $messages = $session->messages()->with('sender')->get();
        // Mark admin messages as read
        $session->messages()->where('sender_type', 'admin')->update(['is_read' => true]);
        $session->update(['unread_count_customer' => 0]);

        return response()->json($messages);
    }

    public function getSessionStatus(ChatSession $session)
    {
        abort_if($session->customer_id !== auth()->id(), 403);

        return response()->json([
            'status'         => $session->status,
            'queue_position' => $session->queue_position,
            'admin_name'     => $session->admin?->name,
            'session_id'     => $session->id,
        ]);
    }

    private function getQueueData(): array
    {
        $queue = ChatSession::fifoQueue()->with('customer')->get();
        return [
            'total_waiting' => $queue->count(),
            'queue'         => $queue->map(fn($s) => [
                'id'             => $s->id,
                'customer_name'  => $s->customer->name,
                'subject'        => $s->subject,
                'waiting_time'   => $s->waiting_time,
                'queue_position' => $queue->search(fn($q) => $q->id === $s->id) + 1,
            ])->values(),
        ];
    }
}
