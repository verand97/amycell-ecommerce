<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Events\NewChatMessage;
use App\Events\ChatQueueUpdated;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function dashboard()
    {
        // FIFO Queue: sessions sorted by created_at ASC
        $waitingQueue = ChatSession::fifoQueue()
            ->with(['customer', 'lastMessage'])
            ->get()
            ->map(function ($session, $index) {
                $session->fifo_position = $index + 1;
                return $session;
            });

        $activeSessions = ChatSession::active()
            ->with(['customer', 'admin', 'lastMessage'])
            ->latest()
            ->get();

        $closedToday = ChatSession::where('status', 'closed')
            ->whereDate('closed_at', today())
            ->count();

        $totalWaiting = $waitingQueue->count();
        $totalActive  = $activeSessions->count();

        return view('admin.chat.dashboard', compact(
            'waitingQueue',
            'activeSessions',
            'closedToday',
            'totalWaiting',
            'totalActive'
        ));
    }

    public function acceptSession(ChatSession $session)
    {
        abort_if($session->status !== 'waiting', 422);

        $session->update([
            'admin_id'    => auth()->id(),
            'status'      => 'active',
            'accepted_at' => now(),
        ]);

        // Send system welcome message
        $message = ChatMessage::create([
            'session_id'  => $session->id,
            'sender_id'   => auth()->id(),
            'sender_type' => 'admin',
            'message'     => 'Halo! Saya ' . auth()->user()->name . ' dari Tim Amycell. Ada yang bisa saya bantu?',
        ]);
        $message->load('sender');

        try {
            broadcast(new ChatQueueUpdated($this->getQueueData()));
            broadcast(new NewChatMessage($message, $session));
        } catch (\Exception $e) {
            Log::warning('Broadcast chat accept gagal: ' . ($e->getMessage() ?? 'Unknown error'));
        }

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
            'message'    => 'Sesi chat diterima.',
        ]);
    }

    public function sendMessage(Request $request, ChatSession $session)
    {
        abort_if($session->status !== 'active', 422);
        abort_if($session->admin_id !== auth()->id(), 403);

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
            'sender_type' => 'admin',
            'message'     => $request->message,
            'attachment'  => $attachmentPath,
        ]);

        $session->increment('unread_count_customer');
        $message->load('sender');

        try {
            broadcast(new NewChatMessage($message, $session));
        } catch (\Exception $e) {
            Log::warning('Broadcast chat message gagal: ' . ($e->getMessage() ?? 'Unknown error'));
        }

        return response()->json([
            'success'    => true,
            'message_id' => $message->id,
            'created_at' => $message->created_at->toISOString(),
        ]);
    }

    public function getMessages(ChatSession $session)
    {
        $messages = $session->messages()->with('sender')->get();
        $session->messages()->where('sender_type', 'customer')->update(['is_read' => true]);
        $session->update(['unread_count_admin' => 0]);

        return response()->json($messages);
    }

    public function closeSession(ChatSession $session)
    {
        $session->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        try {
            broadcast(new ChatQueueUpdated($this->getQueueData()));
        } catch (\Exception $e) {
            Log::warning('Broadcast chat close gagal: ' . ($e->getMessage() ?? 'Unknown error'));
        }

        return response()->json(['success' => true, 'message' => 'Sesi chat ditutup.']);
    }

    public function getQueue()
    {
        return response()->json($this->getQueueData());
    }

    private function getQueueData(): array
    {
        $queue = ChatSession::fifoQueue()->with('customer')->get();
        return [
            'total_waiting' => $queue->count(),
            'queue' => $queue->map(fn($s, $i) => [
                'id'             => $s->id,
                'customer_name'  => $s->customer->name,
                'subject'        => $s->subject,
                'waiting_time'   => $s->waiting_time,
                'queue_position' => $i + 1,
            ])->values(),
        ];
    }
}
