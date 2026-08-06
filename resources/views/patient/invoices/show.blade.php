<x-layouts.patient>
    @section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#1e293b]">{{ __('patient.invoices.invoice_title', ['n' => $invoice->invoice_number]) }}</h1>
                <p class="text-sm text-[#64748b] mt-1">{{ $invoice->labo->name ?? __('patient.invoices.laboratory') }} &middot; {{ $invoice->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('patient.invoices.print', $invoice->id) }}" target="_blank" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                    {{ __('common.print') }}
                </a>
                <a href="{{ route('patient.invoices.index') }}" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-3 py-2 rounded-xl text-xs transition">{{ __('common.back') }}</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-semibold">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden">
                    <div class="p-4 bg-[#F8FAFC] border-b border-[#e2e8f0] flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider">{{ __('patient.invoices.exams') }}</h3>
                        <span class="text-[10px] text-[#64748b]">{{ __('patient.invoices.items_count', ['n' => $invoice->items->count()]) }}</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead><tr class="bg-[#F8FAFC]/50 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                            <th class="p-3 text-left">{{ __('patient.invoices.exam') }}</th>
                            <th class="p-3 text-center">{{ __('patient.invoices.qty') }}</th>
                            <th class="p-3 text-right">{{ __('patient.invoices.price') }}</th>
                            <th class="p-3 text-right">{{ __('patient.invoices.total') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="p-3 font-medium">{{ $item->description ?? $item->exam->name ?? '-' }}</td>
                                <td class="p-3 text-center">{{ $item->quantity }}</td>
                                <td class="p-3 text-right">{{ number_format($item->unit_price, 3) }}</td>
                                <td class="p-3 text-right font-bold">{{ number_format($item->total, 3) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($invoice->items->where('cnam_code')->isNotEmpty())
                <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden">
                    <div class="p-4 bg-[#F8FAFC] border-b border-[#e2e8f0] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                        <h3 class="text-xs font-bold text-[#7C3AED] uppercase tracking-wider">{{ __('patient.invoices.cnam_coverage_title') }}</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead><tr class="bg-[#F8FAFC]/50 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                            <th class="p-3 text-left">{{ __('patient.invoices.exam') }}</th>
                            <th class="p-3 text-center">{{ __('patient.invoices.cnam_code') }}</th>
                            <th class="p-3 text-center">{{ __('patient.invoices.value_b') }}</th>
                            <th class="p-3 text-center">{{ __('patient.invoices.rate') }}</th>
                            <th class="p-3 text-right">{{ __('patient.invoices.coverage') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                                @if($item->cnam_code)
                                <tr>
                                    <td class="p-3 font-medium text-xs">{{ $item->description ?? $item->exam->name ?? '-' }}</td>
                                    <td class="p-3 text-center text-[10px] font-mono text-[#7C3AED] font-bold">{{ $item->cnam_code }}</td>
                                    <td class="p-3 text-center">{{ number_format($item->valeur_b, 3) }}</td>
                                    <td class="p-3 text-center">{{ $invoice->patient->cnamAffiliation->rate->taux ?? '...' }}%</td>
                                    <td class="p-3 text-right font-bold text-emerald-600">{{ number_format($item->cnam_coverage, 3) }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($invoice->examRequest && $invoice->examRequest->doctor)
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-4">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-2">{{ __('patient.invoices.prescriber') }}</h3>
                    <p class="text-sm font-medium">{{ $invoice->examRequest->doctor->user->first_name ?? '' }} {{ $invoice->examRequest->doctor->user->last_name ?? '' }}</p>
                    <p class="text-xs text-[#64748b]">{{ $invoice->examRequest->doctor->specialty ?? '' }}</p>
                </div>
                @endif

                @if($invoice->payments->count() > 0)
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-4">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-3">{{ __('patient.invoices.payments') }}</h3>
                    <table class="w-full text-sm">
                        <thead><tr class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                            <th class="p-2 text-left">{{ __('patient.invoices.date') }}</th>
                            <th class="p-2 text-left">{{ __('patient.invoices.method') }}</th>
                            <th class="p-2 text-right">{{ __('patient.invoices.amount') }}</th>
                            <th class="p-2 text-center">{{ __('common.status') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="p-2">{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                <td class="p-2">{{ $payment->payment_method === 'cash' ? __('patient.invoices.method_cash') : ($payment->payment_method === 'card' ? __('patient.invoices.method_card') : ($payment->payment_method === 'cheque' ? __('patient.invoices.method_cheque') : ($payment->payment_method === 'bank_transfer' ? __('patient.invoices.method_transfer') : __('patient.invoices.method_online')))) }}</td>
                                <td class="p-2 text-right font-bold text-emerald-600">{{ number_format($payment->amount, 3) }}</td>
                                <td class="p-2 text-center">
                                    @if($payment->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">{{ __('patient.invoices.status_pending') }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">{{ __('patient.invoices.payment_confirmed') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($invoice->examRequest && $invoice->examRequest->status === 'completed' && $invoice->examRequest->approved_by_doctor)
                <a href="{{ route('patient.print-exam-request', $invoice->examRequest->id) }}" target="_blank" class="block border border-[#e2e8f0] hover:bg-[#f8fafc] rounded-2xl p-4 transition text-center">
                    <svg class="w-8 h-8 mx-auto text-[#7C3AED] mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <span class="text-xs font-bold text-[#1e293b]">{{ __('patient.invoices.view_report') }}</span>
                </a>
                @endif
            </div>

            <div class="space-y-4">
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 space-y-3">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider">{{ __('patient.invoices.summary') }}</h3>
                    <div class="flex justify-between text-sm"><span class="text-[#64748b]">{{ __('patient.invoices.total') }}</span><span class="font-bold">{{ number_format($invoice->total_amount, 3) }} TND</span></div>
                    @if($invoice->cnam_amount > 0)
                    <div class="flex justify-between text-sm"><span class="text-[#64748b]">{{ __('patient.invoices.cnam_share') }}</span><span class="font-bold text-[#7C3AED]">- {{ number_format(min($invoice->cnam_amount, $invoice->total_amount), 3) }} TND</span></div>
                    @endif
                    <div class="border-t border-[#e2e8f0] pt-3 flex justify-between text-sm"><span class="font-bold">{{ __('patient.invoices.net_to_pay') }}</span><span class="font-bold text-lg">{{ number_format($invoice->patient_amount, 3) }} TND</span></div>
                    @if($invoice->paid_amount > 0)
                    <div class="flex justify-between text-sm"><span class="text-[#64748b]">{{ __('patient.invoices.paid') }}</span><span class="font-bold text-emerald-600">{{ number_format($invoice->paid_amount, 3) }} TND</span></div>
                    @endif
                    @if($invoice->balance > 0)
                    <div class="flex justify-between text-sm"><span class="text-[#64748b]">{{ __('patient.invoices.balance') }}</span><span class="font-bold text-amber-600">{{ number_format($invoice->balance, 3) }} TND</span></div>
                    @endif
                    <div class="pt-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase
                            @if($invoice->status === 'paid') bg-emerald-100 text-emerald-700
                            @elseif($invoice->status === 'partially_paid') bg-amber-100 text-amber-700
                            @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-700 @endif">
                            {{ $invoice->status === 'paid' ? __('patient.invoices.status_paid') : ($invoice->status === 'partially_paid' ? __('patient.invoices.status_partial_payment') : ($invoice->status === 'cancelled' ? __('patient.invoices.status_cancelled') : __('patient.invoices.status_pending'))) }}
                        </span>
                    </div>
                </div>

                @if(!in_array($invoice->status, ['paid', 'cancelled']) && $invoice->balance > 0)
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 space-y-4">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider">{{ __('patient.invoices.make_payment') }}</h3>

                    <form method="POST" action="{{ route('patient.invoices.pay', $invoice->id) }}" id="payment-form">
                        @csrf

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('patient.invoices.amount_to_pay') }}</label>
                            <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl px-4 py-2.5">
                                <span class="text-sm font-bold text-[#1e293b]">{{ number_format($invoice->balance, 3) }}</span>
                                <span class="text-xs font-bold text-[#94a3b8]">TND</span>
                                <input type="hidden" name="amount" value="{{ number_format($invoice->balance, 3, '.', '') }}">
                            </div>
                        </div>

                        <div class="space-y-1.5 mt-4">
                            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('patient.invoices.payment_method') }}</label>
                            <div class="grid grid-cols-2 gap-2" id="payment-methods">
                                <label class="payment-option cursor-pointer border-2 rounded-xl p-3 flex items-center gap-2 transition text-xs font-bold
                                    @if(old('payment_method') === 'card' || old('payment_method') === '') border-[#7C3AED] bg-[#7C3AED]/5 @else border-[#e2e8f0] hover:border-[#cbd5e1] @endif"
                                    data-method="card" onclick="selectPaymentMethod('card')">
                                    <input type="radio" name="payment_method" value="card" class="hidden" {{ old('payment_method') === 'card' || old('payment_method') === '' ? 'checked' : '' }}>
                                    <svg class="w-5 h-5 text-[#7C3AED] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                    <span>{{ __('patient.invoices.method_card') }}</span>
                                </label>
                                <label class="payment-option cursor-pointer border-2 rounded-xl p-3 flex items-center gap-2 transition text-xs font-bold
                                    @if(old('payment_method') === 'cash') border-[#7C3AED] bg-[#7C3AED]/5 @else border-[#e2e8f0] hover:border-[#cbd5e1] @endif"
                                    data-method="cash" onclick="selectPaymentMethod('cash')">
                                    <input type="radio" name="payment_method" value="cash" class="hidden" {{ old('payment_method') === 'cash' ? 'checked' : '' }}>
                                    <svg class="w-5 h-5 text-[#059669] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    <span>{{ __('patient.invoices.method_cash') }}</span>
                                </label>
                                <label class="payment-option cursor-pointer border-2 rounded-xl p-3 flex items-center gap-2 transition text-xs font-bold
                                    @if(old('payment_method') === 'cheque') border-[#7C3AED] bg-[#7C3AED]/5 @else border-[#e2e8f0] hover:border-[#cbd5e1] @endif"
                                    data-method="cheque" onclick="selectPaymentMethod('cheque')">
                                    <input type="radio" name="payment_method" value="cheque" class="hidden" {{ old('payment_method') === 'cheque' ? 'checked' : '' }}>
                                    <svg class="w-5 h-5 text-[#2563EB] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>
                                    <span>{{ __('patient.invoices.method_cheque') }}</span>
                                </label>
                                <label class="payment-option cursor-pointer border-2 rounded-xl p-3 flex items-center gap-2 transition text-xs font-bold
                                    @if(old('payment_method') === 'bank_transfer') border-[#7C3AED] bg-[#7C3AED]/5 @else border-[#e2e8f0] hover:border-[#cbd5e1] @endif"
                                    data-method="bank_transfer" onclick="selectPaymentMethod('bank_transfer')">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="hidden" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                    <svg class="w-5 h-5 text-[#D97706] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                                    <span>{{ __('patient.invoices.method_transfer') }}</span>
                                </label>
                                <label class="payment-option cursor-pointer border-2 rounded-xl p-3 flex items-center gap-2 transition text-xs font-bold col-span-2 opacity-40 cursor-not-allowed"
                                    title="{{ __('patient.invoices.online_unavailable') }}">
                                    <input type="radio" name="payment_method" value="online" class="hidden" disabled>
                                    <svg class="w-5 h-5 text-[#94a3b8] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    <span class="text-[#94a3b8]">{{ __('patient.invoices.method_online') }}</span>
                                    <span class="text-[9px] text-[#94a3b8] font-normal ml-auto">{{ __('patient.invoices.coming_soon') }}</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full mt-4 bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold text-sm px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            {{ __('patient.invoices.confirm_payment') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function selectPaymentMethod(method) {
        document.querySelectorAll('#payment-methods .payment-option').forEach(el => {
            el.classList.remove('border-[#7C3AED]', 'bg-[#7C3AED]/5');
            el.classList.add('border-[#e2e8f0]', 'hover:border-[#cbd5e1]');
        });
        const selected = document.querySelector(`#payment-methods .payment-option[data-method="${method}"]`);
        if (selected) {
            selected.classList.remove('border-[#e2e8f0]', 'hover:border-[#cbd5e1]');
            selected.classList.add('border-[#7C3AED]', 'bg-[#7C3AED]/5');
            selected.querySelector('input[type="radio"]').checked = true;
        }
    }
    </script>
    @endsection
</x-layouts.patient>