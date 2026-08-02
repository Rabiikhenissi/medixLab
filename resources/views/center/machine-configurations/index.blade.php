@extends('layouts.center')

@section('title', 'Configuration Machine')

@section('styles')
<style>
    .mc-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        transition: box-shadow .2s;
    }
    .mc-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
    }
    .mc-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .mc-protocol {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }
</style>
@endsection

@section('content')
<div class="w-full max-w-[1100px] mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-xl font-bold text-[#1e293b]">Configuration Machine</h1>
            <p class="text-xs text-[#64748b] mt-1">Connectez votre laboratoire à un analyseur HL7 (TCP ou Série), RS-232/USB ou HTTP</p>
        </div>
        <a href="{{ route('center.machine-configurations.create') }}"
           class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-[#7C3AED] text-white rounded-xl text-xs font-bold hover:bg-[#6D28D9] transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nouvelle machine
        </a>
    </div>

    {{-- Machine Configurations --}}
    @if($configs->isEmpty())
        <div class="text-center py-20 bg-white border border-[#e2e8f0] rounded-2xl">
            <svg class="w-14 h-14 mx-auto mb-4 text-[#cbd5e1]" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-base font-bold text-[#1e293b] mb-1">Aucune machine configurée</h3>
            <p class="text-xs text-[#94a3b8] mb-5">Ajoutez une machine pour envoyer les examens automatiquement.</p>
            <a href="{{ route('center.machine-configurations.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#7C3AED] text-white rounded-xl text-xs font-bold hover:bg-[#6D28D9] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Ajouter une machine
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($configs as $config)
                <div class="mc-card relative">
                    {{-- Status dot --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-[#7C3AED]/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1e293b]">{{ $config->name }}</h3>
                                <span class="mc-protocol">{{ strtoupper(str_replace('_', ' ', $config->protocol)) }}</span>
                            </div>
                        </div>
                        <span class="mc-badge {{ $config->enabled ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-400 border border-slate-200' }}">
                            {{ $config->enabled ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    {{-- Connection details --}}
                    <div class="space-y-1.5 text-xs text-[#64748b] mb-5">
                        @if($config->protocol === 'serial_hl7')
                            <div class="flex justify-between">
                                <span>Port série</span>
                                <strong class="text-[#1e293b] font-mono">{{ $config->serial_port ?? '—' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Débit</span>
                                <strong class="text-[#1e293b] font-mono">{{ $config->baud_rate }} baud</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Format</span>
                                <strong class="text-[#1e293b] font-mono">{{ $config->data_bits }}{{ $config->parity }}{{ $config->stop_bits }}</strong>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span>Hôte</span>
                                <strong class="text-[#1e293b] font-mono">{{ $config->host }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Port</span>
                                <strong class="text-[#1e293b] font-mono">{{ $config->protocol === 'hl7_mllp' && $config->mllp_port ? $config->mllp_port : $config->port }}</strong>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Timeout</span>
                            <strong class="text-[#1e293b]">{{ $config->timeout }}s</strong>
                        </div>
                        @if($config->api_key)
                            <div class="flex justify-between">
                                <span>API Key</span>
                                <strong class="text-[#1e293b] font-mono">••••••••</strong>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button type="button" onclick="testMachine({{ $config->id }}, this)"
                            class="flex-1 text-center text-xs font-semibold py-2 px-3 bg-white text-[#7C3AED] border border-[#7C3AED] rounded-xl hover:bg-[#f5f3ff] transition test-btn">
                            <span class="btn-text">Tester</span>
                            <svg class="btn-spinner hidden w-4 h-4 animate-spin mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </button>
                        <a href="{{ route('center.machine-configurations.edit', $config) }}"
                           class="flex-1 text-center text-xs font-semibold py-2 px-3 bg-white text-[#64748b] border border-[#e2e8f0] rounded-xl hover:bg-[#f8fafc] transition">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('center.machine-configurations.destroy', $config) }}"
                            onsubmit="return confirm('Supprimer cette configuration machine ?');" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full text-xs font-semibold py-2 px-3 bg-white text-red-500 border border-red-200 rounded-xl hover:bg-red-50 transition">
                                Suppr.
                            </button>
                        </form>
                    </div>

                    {{-- Test result feedback --}}
                    <div class="test-result mt-3 text-[10px] font-semibold hidden"></div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    async function testMachine(id, btn) {
        const card = btn.closest('.mc-card');
        const resultEl = card.querySelector('.test-result');
        const btnText = btn.querySelector('.btn-text');
        const btnSpinner = btn.querySelector('.btn-spinner');

        btn.disabled = true;
        btnText.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        resultEl.classList.add('hidden');

        try {
            const res = await fetch('{{ url("center/machine-configurations") }}/' + id + '/test', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();

            resultEl.classList.remove('hidden');
            if (data.online) {
                const info = data.info ? ' — ' + (data.info.name || data.info.status || 'Connecté') : '';
                resultEl.className = 'test-result mt-3 text-[10px] font-semibold text-emerald-600';
                resultEl.textContent = '✓ Connecté' + info;
            } else {
                resultEl.className = 'test-result mt-3 text-[10px] font-semibold text-red-500';
                resultEl.textContent = '✗ Machine inaccessible';
            }
        } catch (e) {
            resultEl.classList.remove('hidden');
            resultEl.className = 'test-result mt-3 text-[10px] font-semibold text-red-500';
            resultEl.textContent = '✗ Erreur de connexion';
        }

        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
    }
</script>
@endsection