@extends('layouts.center')

@section('title', __('center.billing.index_title').' - Medix eSanté')

@section('content')
<div class="space-y-6 select-none">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#1e293b]">{{ __('center.billing.index_title') }}</h1>
            <p class="text-sm text-[#64748b] mt-2">{{ __('center.billing.index_subtitle') }}</p>
        </div>
        <a href="{{ route('center.billing.create') }}" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-md shadow-[#7C3AED]/20 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('center.billing.new_invoice') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-x-auto shadow-xs">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.billing.invoice_no') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('doctor.patient_label') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('center.billing.amount') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('center.billing.cnam_part') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('center.billing.patient_part') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">{{ __('common.status') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('common.date') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-[#F8FAFC]/70 transition">
                    <td class="p-4 font-mono font-bold text-[#1e293b]">{{ $invoice->invoice_number }}</td>
                    <td class="p-4">{{ $invoice->patient->user->first_name }} {{ $invoice->patient->user->last_name }}</td>
                    <td class="p-4 text-right font-bold">{{ number_format($invoice->total_amount, 3) }} TND</td>
                    <td class="p-4 text-right text-[#7C3AED] font-semibold">{{ number_format($invoice->cnam_amount, 3) }}</td>
                    <td class="p-4 text-right">{{ number_format($invoice->patient_amount, 3) }}</td>
                    <td class="p-4 text-center">
                        @if($invoice->status === 'paid')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">{{ __('center.status.paid') }}</span>
                        @elseif($invoice->status === 'partially_paid')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">{{ __('center.status.partial') }}</span>
                        @elseif($invoice->status === 'cancelled')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">{{ __('center.status.cancelled') }}</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ __('center.status.pending') }}</span>
                        @endif
                    </td>
                    <td class="p-4 text-[#64748b]">{{ $invoice->created_at->format(__('center.date_format')) }}</td>
                    <td class="p-4 text-right">
                        <a href="{{ route('center.billing.show', $invoice->id) }}" class="text-[#7C3AED] hover:text-[#5B21B6] font-bold text-xs">{{ __('common.view') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-8 text-center text-[#94a3b8]">{{ __('center.billing.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
