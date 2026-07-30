@extends('layouts.center')

@section('title', $config ? 'Modifier la machine' : 'Nouvelle machine')

@section('content')
<div class="w-full max-w-[700px] mx-auto py-8 px-4">

    <a href="{{ route('center.machine-configurations.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#7C3AED] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux configurations
    </a>

    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 md:p-8 shadow-sm">
        <h1 class="text-lg font-bold text-[#1e293b] mb-1">{{ $config ? 'Modifier la machine' : 'Nouvelle machine' }}</h1>
        <p class="text-xs text-[#64748b] mb-6">Configurez les paramètres de connexion à votre analyseur de laboratoire.</p>

        <form method="POST" action="{{ $config ? route('center.machine-configurations.update', $config) : route('center.machine-configurations.store') }}" class="space-y-5">
            @csrf
            @if($config) @method('PUT') @endif

            {{-- Name --}}
            <div>
                <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Nom de la machine</label>
                <input type="text" name="name" value="{{ old('name', $config->name ?? '') }}" required maxlength="255"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]"
                    placeholder="ex: Analyseur Hématologie">
                @error('name') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Protocol --}}
            <div>
                <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Protocole</label>
                <select name="protocol" id="protocolSelect" required
                    class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                    <option value="hl7_mllp" {{ old('protocol', $config->protocol ?? 'hl7_mllp') === 'hl7_mllp' ? 'selected' : '' }}>HL7 / MLLP (TCP) — Recommandé</option>
                    <option value="http_json" {{ old('protocol', $config->protocol ?? '') === 'http_json' ? 'selected' : '' }}>HTTP / JSON</option>
                </select>
                @error('protocol') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Host --}}
            <div>
                <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Hôte / Adresse IP</label>
                <input type="text" name="host" value="{{ old('host', $config->host ?? '127.0.0.1') }}" required maxlength="255"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]"
                    placeholder="ex: 192.168.1.100">
                @error('host') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Port + MLLP Port --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Port HTTP</label>
                    <input type="number" name="port" value="{{ old('port', $config->port ?? '5000') }}" required min="1" max="65535"
                        class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                    @error('port') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div id="mllpPortGroup">
                    <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Port MLLP (TCP)</label>
                    <input type="number" name="mllp_port" value="{{ old('mllp_port', $config->mllp_port ?? '') }}" min="1" max="65535"
                        class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]"
                        placeholder="ex: 5001">
                    @error('mllp_port') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Timeout --}}
            <div>
                <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Timeout (secondes)</label>
                <input type="number" name="timeout" value="{{ old('timeout', $config->timeout ?? '15') }}" required min="1" max="300"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]">
                @error('timeout') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- API Key --}}
            <div>
                <label class="block text-xs font-bold text-[#1e293b] mb-1.5">Clé API (optionnelle)</label>
                <input type="text" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}" maxlength="255"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED]"
                    placeholder="ex: token d'authentification">
                @error('api_key') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Enabled --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="enabled" id="enabled" value="1"
                    {{ old('enabled', $config->enabled ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-[#e2e8f0] text-[#7C3AED] focus:ring-[#7C3AED]/20">
                <label for="enabled" class="text-sm font-semibold text-[#1e293b]">Machine active</label>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-[#7C3AED] text-white rounded-xl text-xs font-bold hover:bg-[#6D28D9] transition">
                    {{ $config ? 'Enregistrer les modifications' : 'Ajouter la machine' }}
                </button>
                <a href="{{ route('center.machine-configurations.index') }}"
                   class="px-6 py-2.5 bg-white text-[#64748b] border border-[#e2e8f0] rounded-xl text-xs font-bold hover:bg-[#f8fafc] transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.getElementById('protocolSelect')?.addEventListener('change', function() {
        const mllpGroup = document.getElementById('mllpPortGroup');
        if (mllpGroup) {
            mllpGroup.style.opacity = this.value === 'hl7_mllp' ? '1' : '0.4';
        }
    });
    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('protocolSelect');
        if (sel) sel.dispatchEvent(new Event('change'));
    });
</script>
@endsection