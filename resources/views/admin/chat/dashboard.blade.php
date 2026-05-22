@extends('admin.layouts.app')

@section('title', 'Dashboard Chat FIFO')
@section('page-title', 'Dashboard Live Chat')
@section('page-subtitle', 'Antrian FIFO · First In First Out')

@section('content')
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-slate-900 border border-amber-500/20 rounded-2xl p-4 text-center">
        <p id="total-waiting" class="text-2xl font-black text-amber-400">{{ $totalWaiting }}</p>
        <p class="text-xs text-slate-400 mt-1">Dalam Antrian</p>
    </div>
    <div class="bg-slate-900 border border-sky-500/20 rounded-2xl p-4 text-center">
        <p id="total-active" class="text-2xl font-black text-sky-400">{{ $totalActive }}</p>
        <p class="text-xs text-slate-400 mt-1">Sesi Aktif</p>
    </div>
    <div class="bg-slate-900 border border-emerald-500/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-emerald-400">{{ $closedToday }}</p>
        <p class="text-xs text-slate-400 mt-1">Selesai Hari Ini</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4">

    {{-- FIFO Waiting Queue --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-white">⏳ Antrian Menunggu</h3>
                <p class="text-xs text-slate-500 mt-0.5">FIFO · Pelanggan pertama masuk, pertama dilayani</p>
            </div>
            <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 text-xs font-bold rounded-full" id="queue-badge">{{ $totalWaiting }}</span>
        </div>

        <div id="waiting-queue" class="divide-y divide-slate-800">
            @forelse($waitingQueue as $session)
            <div id="queue-item-{{ $session->id }}" class="px-5 py-4 flex items-center gap-4 hover:bg-slate-800/40 transition-colors">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 font-black text-sm
                    {{ $session->fifo_position === 1 ? 'bg-amber-500 text-amber-900' : 'bg-slate-800 text-slate-400' }}">
                    {{ $session->fifo_position }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-200 truncate">{{ $session->customer->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $session->subject }}</p>
                    <p class="text-[10px] text-amber-400 mt-0.5">Menunggu {{ $session->waiting_time }}</p>
                </div>
                <button onclick="acceptSession({{ $session->id }}, '{{ addslashes($session->customer->name) }}')"
                    class="shrink-0 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-sky-500/20">
                    Terima Chat
                </button>
            </div>
            @empty
            <div id="empty-queue" class="text-center py-10 text-slate-500">
                <div class="text-3xl mb-2">🎉</div>
                <p class="text-sm">Tidak ada pelanggan dalam antrian</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Active Sessions --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white">💬 Sesi Chat Aktif</h3>
            <span id="active-badge" class="px-2.5 py-1 bg-sky-500/20 text-sky-400 text-xs font-bold rounded-full">{{ $totalActive }}</span>
        </div>

        <div id="chat-panel" class="flex" style="height: 520px;">

            {{-- Sessions List --}}
            <div id="sessions-list" class="w-52 border-r border-slate-800 shrink-0 overflow-y-auto">
                @forelse($activeSessions as $session)
                <button onclick="loadChat({{ $session->id }}, '{{ addslashes($session->customer->name) }}')"
                    id="session-btn-{{ $session->id }}"
                    class="w-full px-4 py-3 text-left hover:bg-slate-800 transition-colors border-b border-slate-800/50 active-session-btn">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-sky-500 rounded-full flex items-center justify-center text-[11px] text-white font-bold shrink-0">
                            {{ substr($session->customer->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-200 truncate">{{ $session->customer->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ $session->subject }}</p>
                        </div>
                        @if($session->unread_count_admin > 0)
                            <span class="ml-auto w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center shrink-0" id="unread-{{ $session->id }}">{{ $session->unread_count_admin }}</span>
                        @else
                            <span class="ml-auto w-4 h-4 text-[9px] font-bold rounded-full hidden items-center justify-center shrink-0 bg-red-500 text-white" id="unread-{{ $session->id }}"></span>
                        @endif
                    </div>
                </button>
                @empty
                <div id="no-active-sessions" class="p-6 text-center text-slate-500 text-xs">Tidak ada sesi aktif</div>
                @endforelse
            </div>

            {{-- Chat Area --}}
            <div class="flex-1 flex flex-col min-w-0">
                <div id="chat-header" class="px-4 py-3 border-b border-slate-800 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p id="active-customer-name" class="text-sm font-bold text-slate-200"></p>
                            <p class="text-[10px] text-emerald-400">● Terhubung</p>
                        </div>
                        <button onclick="closeSession()" class="text-[10px] text-red-400 hover:text-red-300 border border-red-500/20 px-3 py-1 rounded-lg transition-colors">Tutup Sesi</button>
                    </div>
                </div>

                <div id="no-chat-selected" class="flex-1 flex items-center justify-center text-slate-600 text-sm">
                    <div class="text-center">
                        <div class="text-4xl mb-2">💬</div>
                        <p>Pilih sesi chat</p>
                    </div>
                </div>

                <div id="admin-messages" class="flex-1 overflow-y-auto p-4 space-y-2 hidden bg-slate-950"></div>

                <div id="admin-input-area" class="hidden p-3 border-t border-slate-800 bg-slate-900">
                    <div class="flex items-end gap-2">
                        <textarea id="admin-msg-input" placeholder="Ketik balasan... (Enter kirim, Shift+Enter baris baru)" rows="2"
                            class="flex-1 px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:ring-2 focus:ring-sky-500 outline-none resize-none"></textarea>
                        <button onclick="sendAdminMessage()" class="p-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const ADMIN_NAME = '{{ addslashes(auth()->user()->name) }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

let activeSessionId = null;
let echoChannels = {}; // track subscribed channels to avoid duplicates

// ─── Helpers ────────────────────────────────────────────────────────────────

function safe(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showToast(msg, type = 'info') {
    const colors = { info: 'bg-sky-500', success: 'bg-emerald-500', error: 'bg-red-500', warning: 'bg-amber-500' };
    const toast = document.createElement('div');
    toast.className = `fixed top-5 right-5 z-50 px-5 py-3 ${colors[type] ?? colors.info} text-white text-sm font-semibold rounded-2xl shadow-2xl transition-all`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// ─── Accept Session ──────────────────────────────────────────────────────────

async function acceptSession(sessionId, customerName) {
    const btn = document.querySelector(`#queue-item-${sessionId} button`);
    if (btn) { btn.disabled = true; btn.textContent = 'Menerima...'; }

    try {
        const res = await fetch(`/admin/chat/session/${sessionId}/accept`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            // Remove from waiting queue
            document.getElementById(`queue-item-${sessionId}`)?.remove();

            // Update waiting counter
            const waitingEl = document.getElementById('total-waiting');
            const badgeEl   = document.getElementById('queue-badge');
            const newCount  = Math.max(0, parseInt(waitingEl.textContent) - 1);
            waitingEl.textContent = newCount;
            badgeEl.textContent   = newCount;

            // Show empty queue message if needed
            const queueDiv = document.getElementById('waiting-queue');
            if (queueDiv && queueDiv.querySelectorAll('[id^="queue-item-"]').length === 0) {
                if (!document.getElementById('empty-queue')) {
                    queueDiv.innerHTML = `<div id="empty-queue" class="text-center py-10 text-slate-500"><div class="text-3xl mb-2">🎉</div><p class="text-sm">Tidak ada pelanggan dalam antrian</p></div>`;
                }
            }

            // Add to active sessions list
            addSessionToList(sessionId, customerName);

            // Update active counter
            const activeEl    = document.getElementById('total-active');
            const activeBadge = document.getElementById('active-badge');
            const newActive   = parseInt(activeEl.textContent) + 1;
            activeEl.textContent    = newActive;
            activeBadge.textContent = newActive;

            // Auto-open the chat
            loadChat(sessionId, customerName);

            showToast(`Chat dengan ${customerName} diterima`, 'success');
        }
    } catch (e) {
        showToast('Gagal menerima chat. Coba lagi.', 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Terima Chat'; }
    }
}

function addSessionToList(sessionId, customerName) {
    const list = document.getElementById('sessions-list');

    // Remove "no active sessions" placeholder
    document.getElementById('no-active-sessions')?.remove();

    // Avoid duplicate
    if (document.getElementById(`session-btn-${sessionId}`)) return;

    const initial = customerName.charAt(0).toUpperCase();
    const btn = document.createElement('button');
    btn.id        = `session-btn-${sessionId}`;
    btn.className = 'w-full px-4 py-3 text-left hover:bg-slate-800 transition-colors border-b border-slate-800/50 active-session-btn';
    btn.onclick   = () => loadChat(sessionId, customerName);
    btn.innerHTML = `
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-sky-500 rounded-full flex items-center justify-center text-[11px] text-white font-bold shrink-0">${safe(initial)}</div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-200 truncate">${safe(customerName)}</p>
                <p class="text-[10px] text-slate-500 truncate">Baru diterima</p>
            </div>
            <span id="unread-${sessionId}" class="ml-auto w-4 h-4 text-[9px] font-bold rounded-full hidden items-center justify-center shrink-0 bg-red-500 text-white"></span>
        </div>`;
    list.appendChild(btn);
}

// ─── Load Chat ───────────────────────────────────────────────────────────────

async function loadChat(sessionId, customerName) {
    activeSessionId = sessionId;

    // UI switch
    document.getElementById('no-chat-selected').classList.add('hidden');
    document.getElementById('admin-messages').classList.remove('hidden');
    document.getElementById('admin-input-area').classList.remove('hidden');
    document.getElementById('chat-header').classList.remove('hidden');
    document.getElementById('active-customer-name').textContent = customerName;

    // Highlight active session button
    document.querySelectorAll('.active-session-btn').forEach(b => b.classList.remove('bg-slate-800'));
    document.getElementById(`session-btn-${sessionId}`)?.classList.add('bg-slate-800');

    // Clear unread badge
    const unreadBadge = document.getElementById(`unread-${sessionId}`);
    if (unreadBadge) unreadBadge.classList.add('hidden');

    // Load messages
    const container = document.getElementById('admin-messages');
    container.innerHTML = '<div class="text-center text-slate-600 text-xs py-4">Memuat pesan...</div>';

    try {
        const res = await fetch(`/admin/chat/session/${sessionId}/messages`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const messages = await res.json();
        container.innerHTML = '';

        if (messages.length === 0) {
            container.innerHTML = '<div class="text-center text-slate-600 text-xs py-4">Belum ada pesan</div>';
        } else {
            messages.forEach(msg => appendAdminMessage(msg.message, msg.sender_type, msg.sender?.name ?? '?'));
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center text-red-400 text-xs py-4">Gagal memuat pesan</div>';
    }

    // Subscribe to Echo channel (avoid duplicate)
    subscribeToSession(sessionId);

    document.getElementById('admin-msg-input')?.focus();
}

function subscribeToSession(sessionId) {
    if (typeof Echo === 'undefined') return;
    if (echoChannels[sessionId]) return; // already subscribed

    echoChannels[sessionId] = Echo.private(`chat.${sessionId}`)
        .listen('.chat.message', (data) => {
            if (data.sender_type === 'customer') {
                // If this session is currently open, show message
                if (activeSessionId === sessionId) {
                    appendAdminMessage(data.message, 'customer', data.sender_name);
                } else {
                    // Show unread badge
                    const badge = document.getElementById(`unread-${sessionId}`);
                    if (badge) {
                        const current = parseInt(badge.textContent) || 0;
                        badge.textContent = current + 1;
                        badge.classList.remove('hidden');
                        badge.style.display = 'flex';
                    }
                    showToast(`Pesan baru dari ${data.sender_name}`, 'info');
                }
            }
        });
}

// ─── Send Message ────────────────────────────────────────────────────────────

async function sendAdminMessage() {
    const input = document.getElementById('admin-msg-input');
    const text  = input.value.trim();
    if (!text || !activeSessionId) return;

    // Optimistic UI
    appendAdminMessage(text, 'admin', ADMIN_NAME);
    input.value = '';
    input.focus();

    try {
        const res = await fetch(`/admin/chat/session/${activeSessionId}/message`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        if (!res.ok) throw new Error('Send failed');
    } catch (e) {
        showToast('Gagal mengirim pesan', 'error');
    }
}

// ─── Close Session ───────────────────────────────────────────────────────────

async function closeSession() {
    if (!activeSessionId) return;
    if (!confirm('Tutup sesi chat ini? Pelanggan tidak bisa mengirim pesan lagi.')) return;

    try {
        await fetch(`/admin/chat/session/${activeSessionId}/close`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' }
        });

        // Remove from sessions list
        document.getElementById(`session-btn-${activeSessionId}`)?.remove();

        // Unsubscribe Echo channel
        if (echoChannels[activeSessionId]) {
            Echo.leave(`chat.${activeSessionId}`);
            delete echoChannels[activeSessionId];
        }

        // Update active counter
        const activeEl    = document.getElementById('total-active');
        const activeBadge = document.getElementById('active-badge');
        const newActive   = Math.max(0, parseInt(activeEl.textContent) - 1);
        activeEl.textContent    = newActive;
        activeBadge.textContent = newActive;

        // Reset chat area
        activeSessionId = null;
        document.getElementById('admin-messages').classList.add('hidden');
        document.getElementById('admin-input-area').classList.add('hidden');
        document.getElementById('chat-header').classList.add('hidden');
        document.getElementById('no-chat-selected').classList.remove('hidden');

        // Show empty sessions if none left
        const list = document.getElementById('sessions-list');
        if (list && list.querySelectorAll('.active-session-btn').length === 0) {
            list.innerHTML = '<div id="no-active-sessions" class="p-6 text-center text-slate-500 text-xs">Tidak ada sesi aktif</div>';
        }

        showToast('Sesi chat ditutup', 'success');
    } catch (e) {
        showToast('Gagal menutup sesi', 'error');
    }
}

// ─── Append Message ──────────────────────────────────────────────────────────

function appendAdminMessage(text, type, name) {
    const isAdmin   = type === 'admin';
    const container = document.getElementById('admin-messages');

    // Remove "no messages" placeholder
    const placeholder = container.querySelector('.text-center');
    if (placeholder) placeholder.remove();

    const div = document.createElement('div');
    div.className = `flex ${isAdmin ? 'justify-end' : 'justify-start'}`;
    div.innerHTML = `
        <div class="max-w-[75%]">
            <p class="text-[9px] text-slate-500 mb-1 ${isAdmin ? 'text-right' : 'text-left'}">${safe(name)}</p>
            <div class="px-3 py-1.5 rounded-2xl text-xs break-words leading-relaxed ${isAdmin ? 'bg-sky-500 text-white rounded-tr-sm' : 'bg-slate-800 text-slate-200 rounded-tl-sm'}">
                ${safe(text)}
            </div>
        </div>`;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

// ─── Keyboard shortcut ───────────────────────────────────────────────────────

document.getElementById('admin-msg-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendAdminMessage(); }
});

// ─── Real-time: Queue Updates via Echo ───────────────────────────────────────

if (typeof Echo !== 'undefined') {
    Echo.private('admin.chat')
        .listen('.chat.queue.updated', (data) => {
            const waitingEl = document.getElementById('total-waiting');
            const badgeEl   = document.getElementById('queue-badge');
            if (waitingEl) waitingEl.textContent = data.total_waiting;
            if (badgeEl)   badgeEl.textContent   = data.total_waiting;

            // Rebuild waiting queue DOM from server data
            if (data.queue && Array.isArray(data.queue)) {
                rebuildWaitingQueue(data.queue);
            }
        })
        .listen('.chat.message', (data) => {
            // New message from customer on any active session
            if (data.sender_type === 'customer' && data.session_id !== activeSessionId) {
                const badge = document.getElementById(`unread-${data.session_id}`);
                if (badge) {
                    const current = parseInt(badge.textContent) || 0;
                    badge.textContent = current + 1;
                    badge.classList.remove('hidden');
                    badge.style.display = 'flex';
                }
            }
        });
}

function rebuildWaitingQueue(queue) {
    const container = document.getElementById('waiting-queue');
    if (!container) return;

    if (queue.length === 0) {
        container.innerHTML = `<div id="empty-queue" class="text-center py-10 text-slate-500"><div class="text-3xl mb-2">🎉</div><p class="text-sm">Tidak ada pelanggan dalam antrian</p></div>`;
        return;
    }

    // Remove empty placeholder
    document.getElementById('empty-queue')?.remove();

    // Add new items that don't exist yet
    queue.forEach((item) => {
        if (!document.getElementById(`queue-item-${item.id}`)) {
            const div = document.createElement('div');
            div.id        = `queue-item-${item.id}`;
            div.className = 'px-5 py-4 flex items-center gap-4 hover:bg-slate-800/40 transition-colors border-b border-slate-800';
            div.innerHTML = `
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 font-black text-sm ${item.queue_position === 1 ? 'bg-amber-500 text-amber-900' : 'bg-slate-800 text-slate-400'}">
                    ${item.queue_position}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-200 truncate">${safe(item.customer_name)}</p>
                    <p class="text-xs text-slate-500 truncate">${safe(item.subject ?? 'Bantuan Umum')}</p>
                    <p class="text-[10px] text-amber-400 mt-0.5">Menunggu ${safe(item.waiting_time)}</p>
                </div>
                <button onclick="acceptSession(${item.id}, '${safe(item.customer_name)}')"
                    class="shrink-0 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition-all">
                    Terima Chat
                </button>`;
            container.appendChild(div);
        }
    });

    // Remove items no longer in queue
    container.querySelectorAll('[id^="queue-item-"]').forEach(el => {
        const id = parseInt(el.id.replace('queue-item-', ''));
        if (!queue.find(q => q.id === id)) el.remove();
    });
}
</script>
@endpush

@endsection
