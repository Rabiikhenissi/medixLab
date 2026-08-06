<x-layouts.doctor>
<x-slot:title>{{ __('doctor.chat.title') }} — {{ __('app.brand') }}</x-slot:title>

@section('styles')
<style>
    .chat-bubble { max-width: 75%; word-wrap: break-word; }
    .chat-scroll { height: calc(100vh - 260px); min-height: 300px; }
    .msg-in { background: #f1f5f9; border-radius: 16px 16px 16px 4px; }
    .msg-out { background: #0D9488; color: white; border-radius: 16px 16px 4px 16px; }
</style>
@endsection

@section('content')
<div class="w-full max-w-[700px] mx-auto flex flex-col" style="height: calc(100vh - 80px);">

    <div class="flex items-center gap-3 px-4 py-3 border-b border-[#e2e8f0] bg-white rounded-t-2xl">
        <a href="{{ route('doctor.dashboard') }}" class="text-[#64748b] hover:text-[#0D9488] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-sm font-bold text-[#1e293b] truncate">{{ $patient->user->first_name }} {{ $patient->user->last_name }}</h2>
            <p class="text-[10px] text-[#94a3b8]">{{ __('doctor.patient_label') }} · {{ $patient->patient_code }}</p>
        </div>
    </div>

    <div id="chatMessages" class="chat-scroll overflow-y-auto px-4 py-4 space-y-3 bg-[#fafbfc] flex-1">
        <div class="text-center py-8">
            <div class="animate-spin w-5 h-5 border-2 border-[#0D9488] border-t-transparent rounded-full mx-auto mb-2"></div>
            <p class="text-xs text-[#94a3b8]">{{ __('common.loading') }}</p>
        </div>
    </div>

    <div class="px-4 py-3 border-t border-[#e2e8f0] bg-white rounded-b-2xl">
        <form id="chatForm" class="flex items-center gap-2">
            @csrf
            <input type="text" id="chatInput" placeholder="{{ __('doctor.chat.message_placeholder') }}"
                class="flex-1 px-4 py-2.5 text-sm rounded-xl border border-[#e2e8f0] bg-[#f8fafc] focus:outline-none focus:ring-2 focus:ring-[#0D9488]/30 focus:border-[#0D9488] transition text-[#1e293b]"
                autocomplete="off" maxlength="2000">
            <button type="submit" id="sendBtn"
                class="w-10 h-10 rounded-xl bg-[#0D9488] hover:bg-[#0a7068] text-white flex items-center justify-center transition-all flex-shrink-0 disabled:opacity-40">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const userId = {{ $user->id }};
    const patientUserId = {{ $patient->user_id }};
    const chatUrl = '{{ route("doctor.chat-messages", $patient) }}';
    const sendUrl = '{{ route("doctor.chat-send", $patient) }}';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const chatEl = document.getElementById('chatMessages');
    const inputEl = document.getElementById('chatInput');
    const formEl = document.getElementById('chatForm');

    async function loadMessages() {
        try {
            const res = await fetch(chatUrl);
            const data = await res.json();
            if (!data.success) return;
            renderMessages(data.messages);
        } catch(e) { console.error(e); }
    }

    function renderMessages(messages) {
        if (messages.length === 0) {
            chatEl.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-xs text-[#94a3b8]">@lang('doctor.chat.empty_state')</p>
                </div>`;
            return;
        }

        chatEl.innerHTML = messages.map(m => {
            const isMe = m.sender_id === userId;
            const time = new Date(m.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            return `
                <div class="flex ${isMe ? 'justify-end' : 'justify-start'}">
                    <div class="chat-bubble ${isMe ? 'msg-out' : 'msg-in'} px-4 py-2.5">
                        <p class="text-sm">${escapeHtml(m.message)}</p>
                        <p class="text-[9px] ${isMe ? 'text-white/60' : 'text-[#94a3b8]'} mt-1 text-right">${time}</p>
                    </div>
                </div>`;
        }).join('');

        chatEl.scrollTop = chatEl.scrollHeight;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    formEl.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = inputEl.value.trim();
        if (!msg) return;
        inputEl.value = '';
        document.getElementById('sendBtn').disabled = true;
        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: msg }),
            });
            const data = await res.json();
            if (data.success) await loadMessages();
        } catch(e) { console.error(e); }
        document.getElementById('sendBtn').disabled = false;
        inputEl.focus();
    });

    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); formEl.dispatchEvent(new Event('submit')); }
    });

    loadMessages();
    setInterval(loadMessages, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) loadMessages(); });
</script>
@endsection
</x-layouts.doctor>
