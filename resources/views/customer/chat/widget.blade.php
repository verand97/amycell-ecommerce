{{-- Floating Live Chat Widget --}}
@auth
@if(auth()->user()->isCustomer())
<div id="chat-widget" class="fixed bottom-6 right-6 z-200" data-user-id="{{ auth()->id() }}">

    {{-- Toggle Button --}}
    <button id="chat-toggle"
        class="w-14 h-14 bg-linear-to-br from-sky-500 to-indigo-600 rounded-full shadow-2xl shadow-sky-300 flex items-center justify-center text-white hover:scale-110 transition-transform relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"/>
        </svg>
        <span id="chat-unread-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full hidden items-center justify-center">!</span>
    </button>

    {{-- Chat Window --}}
    <div id="chat-window" class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="display:none; height:480px;">

        {{-- Header --}}
        <div class="bg-linear-to-r from-sky-500 to-indigo-600 p-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-bold">AC</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Live Chat Amycell</p>
                    <p id="chat-status-text" class="text-sky-200 text-xs">Tim CS siap membantu</p>
                </div>
            </div>
            <button id="chat-close" class="text-white/70 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Status bars --}}
        <div id="queue-indicator" class="hidden bg-amber-50 border-b border-amber-200 px-4 py-2 shrink-0">
            <p class="text-amber-700 text-xs font-semibold">⏳ Antrian ke-<span id="queue-position">-</span> — CS akan segera merespons</p>
        </div>
        <div id="connected-indicator" class="hidden bg-emerald-50 border-b border-emerald-200 px-4 py-2 shrink-0">
            <p class="text-emerald-700 text-xs font-semibold flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse inline-block"></span>
                Terhubung dengan CS Amycell
            </p>
        </div>
        <div id="closed-indicator" class="hidden bg-slate-100 border-b border-slate-200 px-4 py-2 shrink-0">
            <p class="text-slate-500 text-xs text-center">Sesi chat telah ditutup</p>
        </div>

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
            <div class="text-center" id="chat-welcome">
                <p class="text-[11px] text-slate-400 bg-white rounded-full px-3 py-1 inline-block shadow-sm">Selamat datang di Amycell Support! 👋</p>
            </div>
        </div>

        {{-- Start area --}}
        <div id="chat-start-area" class="p-4 border-t border-slate-100 bg-white shrink-0">
            <p class="text-xs text-slate-500 mb-3 text-center">Mulai sesi chat dengan tim kami</p>
            <button id="start-chat-btn" class="w-full py-2.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white text-sm font-bold rounded-xl hover:shadow-lg transition-all">
                💬 Mulai Chat
            </button>
        </div>

        {{-- Input area --}}
        <div id="chat-input-area" class="hidden p-4 border-t border-slate-100 bg-white shrink-0">
            <div class="flex items-end gap-2">
                <textarea id="chat-message-input" placeholder="Ketik pesan... (Enter kirim)" rows="2"
                    class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 outline-none resize-none"></textarea>
                <button id="send-chat-btn" class="p-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ── State ──────────────────────────────────────────────────────────────
    let chatSessionId  = null;
    let sessionStatus  = null;   // 'waiting' | 'active' | 'closed'
    let chatOpen       = false;
    let echoChannel    = null;
    let pollTimer      = null;

    // ── DOM refs ───────────────────────────────────────────────────────────
    const el = id => document.getElementById(id);
    const chatToggle   = el('chat-toggle');
    const chatWindow   = el('chat-window');
    const chatClose    = el('chat-close');
    const messagesDiv  = el('chat-messages');
    const startArea    = el('chat-start-area');
    const inputArea    = el('chat-input-area');
    const startBtn     = el('start-chat-btn');
    const sendBtn      = el('send-chat-btn');
    const msgInput     = el('chat-message-input');
    const queueInd     = el('queue-indicator');
    const queuePos     = el('queue-position');
    const statusText   = el('chat-status-text');
    const connectedInd = el('connected-indicator');
    const closedInd    = el('closed-indicator');
    const unreadBadge  = el('chat-unread-badge');

    const CSRF = '{{ csrf_token() }}';

    // ── Toggle ─────────────────────────────────────────────────────────────
    chatToggle.addEventListener('click', () => {
        chatOpen = !chatOpen;
        chatWindow.style.display = chatOpen ? 'flex' : 'none';
        if (chatOpen) {
            hideUnread();
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    });
    chatClose.addEventListener('click', () => {
        chatOpen = false;
        chatWindow.style.display = 'none';
    });

    // ── Start session ──────────────────────────────────────────────────────
    startBtn.addEventListener('click', async () => {
        startBtn.disabled    = true;
        startBtn.textContent = 'Menghubungkan...';

        try {
            const res  = await fetch('{{ route("customer.chat.start") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                body: JSON.stringify({ subject: 'Bantuan Umum' })
            });
            const data = await res.json();

            chatSessionId = data.session_id;
            sessionStatus = data.status;

            startArea.classList.add('hidden');
            inputArea.classList.remove('hidden');

            // Subscribe Echo FIRST — before anything else
            subscribeEcho();

            if (data.status === 'active') {
                // Already active (returning customer) — load existing messages
                await loadMessages();
                setConnected(data.admin_name);
            } else {
                // Waiting — show queue position and start polling
                setWaiting(data.queue_position);
                startPolling();
            }

        } catch (e) {
            startBtn.disabled    = false;
            startBtn.textContent = '💬 Mulai Chat';
            appendMsg('Gagal terhubung. Coba lagi.', 'system', '');
        }
    });

    // ── Subscribe to Echo channel ──────────────────────────────────────────
    function subscribeEcho() {
        if (echoChannel || typeof Echo === 'undefined' || !chatSessionId) return;

        echoChannel = Echo.private(`chat.${chatSessionId}`)
            .listen('.chat.message', (data) => {
                console.log('[Chat] Received:', data);

                if (data.sender_type === 'admin') {
                    appendMsg(data.message, 'admin', data.sender_name);

                    // If we were waiting, session is now active
                    if (sessionStatus !== 'active') {
                        sessionStatus = 'active';
                        stopPolling();
                        setConnected(data.sender_name);
                    }

                    // Show unread badge if window closed
                    if (!chatOpen) showUnread();
                }
            });

        console.log('[Chat] Subscribed to channel: chat.' + chatSessionId);
    }

    // ── Load existing messages from server ─────────────────────────────────
    async function loadMessages() {
        try {
            const res  = await fetch(
                '{{ route("customer.chat.messages", ["session" => "__SID__"]) }}'.replace('__SID__', chatSessionId),
                { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }
            );
            const msgs = await res.json();

            // Clear welcome message if we have real messages
            if (msgs.length > 0) {
                const welcome = el('chat-welcome');
                if (welcome) welcome.remove();
            }

            msgs.forEach(msg => {
                appendMsg(msg.message, msg.sender_type, msg.sender?.name ?? '');
            });
        } catch (e) {
            console.warn('[Chat] Failed to load messages:', e);
        }
    }

    // ── Poll session status (fallback if Echo not connected) ───────────────
    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(async () => {
            if (!chatSessionId) return;
            try {
                const res  = await fetch(
                    '{{ route("customer.chat.status", ["session" => "__SID__"]) }}'.replace('__SID__', chatSessionId),
                    { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }
                );
                const data = await res.json();

                if (data.status === 'active' && sessionStatus !== 'active') {
                    sessionStatus = 'active';
                    stopPolling();
                    setConnected(data.admin_name);
                    // Load messages that arrived while we were polling
                    await loadMessages();
                } else if (data.status === 'closed' && sessionStatus !== 'closed') {
                    sessionStatus = 'closed';
                    stopPolling();
                    setClosed();
                } else if (data.status === 'waiting') {
                    queuePos.textContent   = data.queue_position;
                    statusText.textContent = `Antrian ke-${data.queue_position}`;
                }
            } catch (e) { /* ignore */ }
        }, 4000);
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    // ── UI state helpers ───────────────────────────────────────────────────
    function setWaiting(pos) {
        queueInd.classList.remove('hidden');
        connectedInd.classList.add('hidden');
        closedInd.classList.add('hidden');
        queuePos.textContent   = pos;
        statusText.textContent = `Antrian ke-${pos}`;
    }

    function setConnected(adminName) {
        queueInd.classList.add('hidden');
        connectedInd.classList.remove('hidden');
        closedInd.classList.add('hidden');
        statusText.textContent = adminName ? `Terhubung dengan ${adminName}` : 'Terhubung dengan CS';
        inputArea.classList.remove('hidden');
        msgInput.focus();
    }

    function setClosed() {
        queueInd.classList.add('hidden');
        connectedInd.classList.add('hidden');
        closedInd.classList.remove('hidden');
        inputArea.classList.add('hidden');
        statusText.textContent = 'Sesi ditutup';
        appendMsg('Sesi chat telah ditutup. Terima kasih telah menghubungi Amycell! 😊', 'system', '');
    }

    function showUnread() {
        unreadBadge.classList.remove('hidden');
        unreadBadge.style.display = 'flex';
    }

    function hideUnread() {
        unreadBadge.style.display = 'none';
        unreadBadge.classList.add('hidden');
    }

    // ── Send message ───────────────────────────────────────────────────────
    async function sendMessage() {
        const text = msgInput.value.trim();
        if (!text || !chatSessionId || sessionStatus === 'closed') return;

        appendMsg(text, 'customer', 'Anda');
        msgInput.value = '';

        try {
            const res = await fetch(
                '{{ route("customer.chat.send", ["session" => "__SID__"]) }}'.replace('__SID__', chatSessionId),
                {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                }
            );
            if (!res.ok) throw new Error('Send failed');
        } catch (e) {
            appendMsg('Pesan gagal terkirim.', 'system', '');
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    // ── Append message to DOM ──────────────────────────────────────────────
    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function appendMsg(text, type, name) {
        const isAdmin  = type === 'admin';
        const isSystem = type === 'system';
        const div      = document.createElement('div');

        if (isSystem) {
            div.className = 'text-center';
            div.innerHTML = `<p class="text-[11px] text-slate-400 bg-white rounded-full px-3 py-1 inline-block shadow-sm">${esc(text)}</p>`;
        } else {
            div.className = `flex ${isAdmin ? 'justify-start' : 'justify-end'}`;
            div.innerHTML = `
                <div class="max-w-[80%]">
                    ${name ? `<p class="text-[10px] text-slate-400 mb-1 ${isAdmin ? 'text-left' : 'text-right'}">${esc(name)}</p>` : ''}
                    <div class="px-3 py-2 rounded-2xl text-sm break-words leading-relaxed
                        ${isAdmin
                            ? 'bg-white border border-slate-200 text-slate-700 rounded-tl-sm shadow-sm'
                            : 'bg-sky-500 text-white rounded-tr-sm'}">
                        ${esc(text)}
                    </div>
                </div>`;
        }

        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

})();
</script>
@endif
@endauth
