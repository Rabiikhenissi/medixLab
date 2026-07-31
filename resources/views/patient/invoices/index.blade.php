<x-layouts.patient>
    @section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Mes Factures</h1>
            <p class="text-sm text-[#64748b] mt-1">Consultez l'historique de vos factures et paiements</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
        @endif

        @forelse($invoices as $invoice)
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-[#1e293b]">{{ $invoice->invoice_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                @if($invoice->status === 'paid') bg-emerald-100 text-emerald-700
                                @elseif($invoice->status === 'partially_paid') bg-amber-100 text-amber-700
                                @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $invoice->status === 'paid' ? 'Payée' : ($invoice->status === 'partially_paid' ? 'Partielle' : ($invoice->status === 'cancelled' ? 'Annulée' : 'En attente')) }}
                            </span>
                        </div>
                        <p class="text-xs text-[#64748b]">
                            <span class="font-medium">{{ $invoice->labo->name ?? 'Laboratoire' }}</span>
                            &middot; {{ $invoice->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-[#1e293b]">{{ number_format($invoice->total_amount, 3) }} TND</p>
                        @if($invoice->paid_amount > 0)
                            <p class="text-[11px] text-emerald-600 font-semibold">Payé: {{ number_format($invoice->paid_amount, 3) }} TND</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-[#f1f5f9]">
                    <a href="{{ route('patient.invoices.show', $invoice->id) }}" class="text-xs font-bold text-[#7C3AED] hover:text-[#5B21B6] transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Détails
                    </a>
                    <a href="{{ route('patient.invoices.print', $invoice->id) }}" target="_blank" class="text-xs font-bold text-[#64748b] hover:text-[#1e293b] transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.048 48.048 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                        Imprimer
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-[#cbd5e1] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                <p class="text-sm text-[#94a3b8] font-medium">Aucune facture pour le moment.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
    @endsection
</x-layouts.patient>
