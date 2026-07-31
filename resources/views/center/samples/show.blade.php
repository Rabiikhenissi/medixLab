@extends('layouts.center')

@section('title', 'Échantillon #'.$sample->sample_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 select-none">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Échantillon <span class="font-mono text-[#7C3AED]">#{{ $sample->sample_code }}</span></h1>
            <p class="text-sm text-[#64748b] mt-1">Créé le {{ $sample->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('center.samples.barcode', $sample->id) }}" target="_blank" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition">Imprimer code-barres</a>
            <a href="{{ route('center.samples.index') }}" class="text-[#64748b] font-medium text-sm px-3 py-2">Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4">Détails</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-[#64748b]">Patient:</span><br><span class="font-bold">{{ $sample->patient->user->first_name }} {{ $sample->patient->user->last_name }}</span></div>
                    <div><span class="text-[#64748b]">Examen:</span><br><span class="font-bold">{{ $sample->examRequestItem->exam->name ?? 'N/A' }}</span></div>
                    <div><span class="text-[#64748b]">Type:</span><br><span class="font-bold">{{ $sample->material_type ?? '-' }}</span></div>
                    <div><span class="text-[#64748b]">Emplacement:</span><br><span class="font-bold">{{ $sample->storage_location ?? '-' }}</span></div>
                    <div><span class="text-[#64748b]">Collecté par:</span><br><span class="font-bold">{{ $sample->collector->user->first_name ?? 'N/A' }} {{ $sample->collector->user->last_name ?? '' }}</span></div>
                    <div><span class="text-[#64748b]">Date collecte:</span><br><span class="font-bold">{{ $sample->collection_date ? $sample->collection_date->format('d/m/Y') : '-' }}</span></div>
                    <div><span class="text-[#64748b]">Expire le:</span><br><span class="font-bold {{ $sample->expiry_date && $sample->expiry_date->isPast() ? 'text-red-600' : '' }}">{{ $sample->expiry_date ? $sample->expiry_date->format('d/m/Y') : '-' }}</span></div>
                    <div><span class="text-[#64748b]">Statut:</span><br>
                        @php $statusClasses = ['pending'=>'bg-slate-100 text-slate-700','collected'=>'bg-blue-100 text-blue-700','in_transit'=>'bg-amber-100 text-amber-700','received'=>'bg-purple-100 text-purple-700','processing'=>'bg-cyan-100 text-cyan-700','completed'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusClasses[$sample->status] ?? 'bg-slate-100' }}">{{ $sample->status }}</span>
                    </div>
                </div>
                @if($sample->rejection_reason)
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <span class="text-xs font-bold text-red-700">Motif de rejet:</span>
                    <p class="text-sm text-red-600 mt-1">{{ $sample->rejection_reason }}</p>
                </div>
                @endif
                @if($sample->notes)
                <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="text-xs font-bold text-[#64748b]">Notes:</span>
                    <p class="text-sm text-[#1e293b] mt-1">{{ $sample->notes }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4">Historique des actions</h3>
                @if($sample->barcodeLogs->count() > 0)
                <div class="space-y-3">
                    @foreach($sample->barcodeLogs as $log)
                    <div class="flex items-start gap-3 pb-3 border-b border-[#e2e8f0]/60 last:border-0">
                        <div class="w-2 h-2 mt-1.5 rounded-full bg-[#7C3AED] flex-shrink-0"></div>
                        <div>
                            <span class="font-bold text-xs uppercase">{{ $log->action }}</span>
                            <p class="text-xs text-[#64748b]">
                                Par {{ $log->staff->user->first_name ?? 'N/A' }} {{ $log->staff->user->last_name ?? '' }}
                                @if($log->location) · {{ $log->location }} @endif
                                · {{ $log->created_at->format('d/m/Y H:i') }}
                            </p>
                            @if($log->notes)<p class="text-xs text-[#94a3b8] mt-0.5">{{ $log->notes }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-sm text-[#94a3b8]">Aucun historique.</p>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-3">Mettre à jour le statut</h3>
                <form method="POST" action="{{ route('center.samples.status', $sample->id) }}" class="space-y-3">
                    @csrf
                    <div>
                        <select name="status" required class="w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-sm focus:border-[#7C3AED] outline-none">
                            <option value="collected" {{ $sample->status === 'collected' ? 'disabled' : '' }}>Collecté</option>
                            <option value="in_transit">En transit</option>
                            <option value="received">Reçu</option>
                            <option value="processing">En traitement</option>
                            <option value="completed">Terminé</option>
                            <option value="rejected">Rejeté</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" name="storage_location" placeholder="Emplacement" value="{{ $sample->storage_location }}" class="w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-sm focus:border-[#7C3AED] outline-none">
                    </div>
                    <div id="rejectionReason" class="hidden">
                        <textarea name="rejection_reason" placeholder="Motif du rejet..." rows="2" class="w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-sm focus:border-[#7C3AED] outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition">Mettre à jour</button>
                </form>
            </div>

            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 text-center">
                <div id="barcodeContainer" class="mb-2"></div>
                <p class="text-[10px] font-mono font-bold text-[#64748b]">{{ $sample->sample_code }}</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode('#barcodeContainer', '{{ $sample->sample_code }}', {
        format: 'CODE128', width: 1.5, height: 40, displayValue: false, margin: 5
    });
    document.querySelector('select[name="status"]').addEventListener('change', function() {
        document.getElementById('rejectionReason').classList.toggle('hidden', this.value !== 'rejected');
    });
</script>
@endsection
