@extends('layouts.center')

@section('title', 'Facture #'.$invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 select-none">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Facture #{{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-[#64748b] mt-1">Créée le {{ $invoice->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('center.billing.print', $invoice->id) }}" target="_blank" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Imprimer
            </a>
            <a href="{{ route('center.billing.traite', $invoice->id) }}" target="_blank" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                Traite
            </a>
            @if($invoice->cnam_amount > 0)
            <a href="{{ route('center.billing.elfatoora', $invoice->id) }}" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                XML ELFATOORA
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden">
                <div class="p-4 bg-[#F8FAFC] border-b border-[#e2e8f0]">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider">Articles</h3>
                </div>
                <table class="w-full text-sm">
                    <thead><tr class="bg-[#F8FAFC]/50 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-center">Qté</th>
                        <th class="p-3 text-right">Prix unit.</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-right">CNAM</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="p-3 font-medium">{{ $item->description }}
                                @if($item->cnam_code)<br><span class="text-[10px] text-[#7C3AED]">CNAM: {{ $item->cnam_code }}</span>@endif
                            </td>
                            <td class="p-3 text-center">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->unit_price, 3) }}</td>
                            <td class="p-3 text-right font-bold">{{ number_format($item->total, 3) }}</td>
                            <td class="p-3 text-right text-[#7C3AED]">{{ number_format($item->cnam_coverage, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-4">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-3">Paiements</h3>
                @if($invoice->payments->count() > 0)
                <table class="w-full text-sm">
                    <thead><tr class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        <th class="p-2 text-left">Date</th>
                        <th class="p-2 text-left">Méthode</th>
                        <th class="p-2 text-right">Montant</th>
                        <th class="p-2 text-center">Statut</th>
                        <th class="p-2 text-left">Référence</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td class="p-2">{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                            <td class="p-2">{{ $payment->payment_method === 'cash' ? 'Espèces' : ($payment->payment_method === 'card' ? 'Carte' : ($payment->payment_method === 'cheque' ? 'Chèque' : ($payment->payment_method === 'bank_transfer' ? 'Virement' : 'En ligne'))) }}</td>
                            <td class="p-2 text-right font-bold text-emerald-600">{{ number_format($payment->amount, 3) }}</td>
                            <td class="p-2 text-center">
                                @if($payment->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        En attente
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Confirmé</span>
                                @endif
                            </td>
                            <td class="p-2 text-[#64748b] text-xs">{{ $payment->transaction_id ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @php $pendingPayment = $invoice->payments->where('status', 'pending')->first(); @endphp
                @if($pendingPayment)
                <form method="POST" action="{{ route('center.payments.confirm', $pendingPayment->id) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-amber-200 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Confirmer le paiement
                    </button>
                </form>
                @endif
                @else
                    <p class="text-sm text-[#94a3b8]">Aucun paiement enregistré.</p>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 space-y-3">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider">Résumé</h3>
                <div class="flex justify-between text-sm"><span class="text-[#64748b]">Total</span><span class="font-bold">{{ number_format($invoice->total_amount, 3) }} TND</span></div>
                <div class="flex justify-between text-sm"><span class="text-[#64748b]">Part CNAM</span><span class="font-bold text-[#7C3AED]">- {{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
                <div class="border-t border-[#e2e8f0] pt-3 flex justify-between text-sm"><span class="font-bold">Part Patient</span><span class="font-bold text-lg">{{ number_format($invoice->patient_amount, 3) }} TND</span></div>
                <div class="flex justify-between text-sm"><span class="text-[#64748b]">Payé</span><span class="font-bold text-emerald-600">{{ number_format($invoice->paid_amount, 3) }} TND</span></div>
                @if($invoice->balance > 0)
                <div class="flex justify-between text-sm"><span class="text-[#64748b]">Reste</span><span class="font-bold text-amber-600">{{ number_format($invoice->balance, 3) }} TND</span></div>
                @endif
                <div class="pt-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase
                        @if($invoice->status === 'paid') bg-emerald-100 text-emerald-700
                        @elseif($invoice->status === 'partially_paid') bg-amber-100 text-amber-700
                        @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700
                        @else bg-slate-100 text-slate-700 @endif">
                        {{ $invoice->status === 'paid' ? 'Payée' : ($invoice->status === 'partially_paid' ? 'Paiement partiel' : ($invoice->status === 'cancelled' ? 'Annulée' : 'En attente')) }}
                    </span>
                </div>
            </div>

            @if(in_array($invoice->status, ['pending', 'partially_paid']))
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5">
                <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4">Enregistrer un paiement</h3>
                <form method="POST" action="{{ route('center.billing.pay', $invoice->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase tracking-wider mb-1.5 block">Montant (TND)</label>
                        <div class="relative">
                            <input type="number" step="0.001" name="amount" max="{{ $invoice->balance }}" required
                                class="w-full border-2 border-[#e2e8f0] focus:border-emerald-500 rounded-xl px-4 py-3 text-lg font-bold text-[#1e293b] outline-none transition"
                                placeholder="0.000">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-[#94a3b8]">TND</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase tracking-wider mb-2 block">Méthode de paiement</label>
                        <div class="grid grid-cols-2 gap-2" id="paymentMethods">
                            <label class="payment-method flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition border-emerald-500 bg-emerald-50" data-value="cash">
                                <input type="radio" name="payment_method" value="cash" checked class="sr-only">
                                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs font-bold text-emerald-700">Espèces</span>
                            </label>
                            <label class="payment-method flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition border-[#e2e8f0] bg-white hover:border-[#cbd5e1]" data-value="card">
                                <input type="radio" name="payment_method" value="card" class="sr-only">
                                <svg class="w-5 h-5 shrink-0 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                <span class="text-xs font-bold text-[#475569]">Carte</span>
                            </label>
                            <label class="payment-method flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition border-[#e2e8f0] bg-white hover:border-[#cbd5e1]" data-value="cheque">
                                <input type="radio" name="payment_method" value="cheque" class="sr-only">
                                <svg class="w-5 h-5 shrink-0 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                                <span class="text-xs font-bold text-[#475569]">Chèque</span>
                            </label>
                            <label class="payment-method flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition border-[#e2e8f0] bg-white hover:border-[#cbd5e1]" data-value="bank_transfer">
                                <input type="radio" name="payment_method" value="bank_transfer" class="sr-only">
                                <svg class="w-5 h-5 shrink-0 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                <span class="text-xs font-bold text-[#475569]">Virement</span>
                            </label>
                            <label class="payment-method flex items-center gap-2 p-3 rounded-xl border-2 border-[#e2e8f0] bg-[#f8fafc] col-span-2 opacity-40 cursor-not-allowed" title="Paiement en ligne non disponible">
                                <input type="radio" name="payment_method" value="online" class="sr-only" disabled>
                                <svg class="w-5 h-5 shrink-0 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                <span class="text-xs font-bold text-[#94a3b8]">En ligne</span>
                                <span class="text-[9px] text-[#94a3b8] font-normal ml-auto">Bientôt</span>
                            </label>
                        </div>
                    </div>
                    <script>
                        document.getElementById('paymentMethods').addEventListener('click', function(e) {
                            var label = e.target.closest('.payment-method');
                            if (!label) return;
                            label.querySelector('input[type="radio"]').checked = true;
                            this.querySelectorAll('.payment-method').forEach(function(l) {
                                l.classList.remove('border-emerald-500', 'bg-emerald-50');
                                l.classList.add('border-[#e2e8f0]', 'bg-white');
                                l.querySelector('svg').classList.remove('text-emerald-600');
                                l.querySelector('svg').classList.add('text-[#94a3b8]');
                                l.querySelector('span').classList.remove('text-emerald-700');
                                l.querySelector('span').classList.add('text-[#475569]');
                            });
                            label.classList.remove('border-[#e2e8f0]', 'bg-white');
                            label.classList.add('border-emerald-500', 'bg-emerald-50');
                            label.querySelector('svg').classList.remove('text-[#94a3b8]');
                            label.querySelector('svg').classList.add('text-emerald-600');
                            label.querySelector('span').classList.remove('text-[#475569]');
                            label.querySelector('span').classList.add('text-emerald-700');
                        });
                    </script>

                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase tracking-wider mb-1.5 block">Réf. Transaction <span class="text-[#94a3b8]">(optionnel)</span></label>
                        <input type="text" name="transaction_id"
                            class="w-full border-2 border-[#e2e8f0] focus:border-emerald-500 rounded-xl px-4 py-2.5 text-sm outline-none transition"
                            placeholder="Numéro de chèque ou réf. virement">
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-200">
                        Confirmer le paiement
                    </button>
                </form>
            </div>
            @endif

            @if(in_array($invoice->status, ['pending', 'partially_paid']))
            <form method="POST" action="{{ route('center.billing.cancel', $invoice->id) }}" onsubmit="return confirm('Annuler cette facture ?')">
                @csrf
                <button type="submit" class="w-full border border-red-200 text-red-600 hover:bg-red-50 font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition">Annuler la facture</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
