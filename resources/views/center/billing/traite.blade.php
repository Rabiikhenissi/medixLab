<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ __('center.traite.title') }} #{{ $invoice->invoice_number }}</title>
<style>
    body { font-family: 'Courier New', monospace; font-size: 11px; color: #000; margin: 20px; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
    .header h1 { font-size: 16px; margin: 0; }
    .info { margin: 10px 0; }
    .info div { display: flex; padding: 2px 0; }
    .info .label { width: 140px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #000; padding: 5px 6px; text-align: left; }
    th { background: #e8e8e8; font-size: 10px; }
    .right { text-align: right; }
    .center { text-align: center; }
    .totals { border-top: 2px solid #000; margin-top: 10px; padding-top: 10px; }
    .totals div { display: flex; justify-content: space-between; padding: 2px 0; }
    .bold { font-weight: bold; }
    .signature { margin-top: 40px; display: flex; justify-content: space-between; }
    .signature div { width: 200px; }
    .signature .line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 10px; text-align: center; }
    .footer { margin-top: 20px; text-align: center; font-size: 9px; }
    @media print { body { margin: 0; } }
</style>
</head>
<body>
<div class="header">
    <h1>{{ strtoupper(__('center.traite.title')) }}</h1>
    <p>{{ __('center.traite.no', ['number' => $invoice->invoice_number]) }}</p>
    <p>{{ __('center.traite.date_prefix') }} {{ $invoice->created_at->format(__('center.date_format')) }}</p>
</div>

<div class="info">
    <div><span class="label">{{ __('center.traite.laboratory') }}</span><span>{{ $invoice->labo->name }}</span></div>
    <div><span class="label">{{ __('center.traite.address') }}</span><span>{{ $invoice->labo->address ?? '' }}</span></div>
    <div><span class="label">{{ __('center.traite.phone') }}</span><span>{{ $invoice->labo->phone ?? '' }}</span></div>
</div>

<div class="info">
    <div><span class="label">{{ __('center.traite.patient') }}</span><span>{{ $invoice->patient->user->first_name }} {{ $invoice->patient->user->last_name }}</span></div>
    @if($invoice->patient->cnamAffiliation)
    <div><span class="label">{{ __('center.traite.cnam_no') }}</span><span>{{ $invoice->patient->cnamAffiliation->cnam_number }}</span></div>
    <div><span class="label">{{ __('center.traite.affiliation_no') }}</span><span>{{ $invoice->patient->cnamAffiliation->affiliation_number ?? '-' }}</span></div>
    <div><span class="label">{{ __('center.traite.cnam_rate') }}</span><span>{{ $invoice->patient->cnamAffiliation->rate->taux ?? 0 }}% ({{ $invoice->patient->cnamAffiliation->rate->label ?? '' }})</span></div>
    @else
    <div><span class="label">{{ __('center.traite.cnam_label') }}</span><span>{{ __('center.traite.not_affiliated') }}</span></div>
    @endif
</div>

<table>
    <thead><tr>
        <th>{{ __('center.cnam.code_cnam') }}</th>
        <th>{{ __('center.traite.act') }}</th>
        <th class="center">{{ __('center.billing.qty') }}</th>
        <th class="right">{{ __('center.traite.valeur_b') }}</th>
        <th class="right">{{ __('center.traite.coeff') }}</th>
        <th class="right">{{ __('center.traite.amount') }}</th>
    </tr></thead>
    <tbody>
        @foreach($invoice->items->where('cnam_code') as $item)
        <tr>
            <td>{{ $item->cnam_code }}</td>
            <td>{{ $item->description }}</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->valeur_b ?? 0, 3) }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->cnam_coverage, 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <div><span>{{ __('center.traite.total_acts') }}</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
    <div class="bold"><span>{{ __('center.traite.total_cnam') }}</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
</div>

<div class="signature">
    <div><div class="line">{{ __('center.traite.lab_signature') }}</div></div>
    <div><div class="line">{{ __('center.traite.patient_signature') }}</div></div>
</div>

<div class="footer">
    <p>{{ __('center.traite.generated', ['brand' => 'Medix eSanté', 'date' => now()->format(__('common.datetime_format'))]) }}</p>
</div>
<script>window.print();</script>
</body>
</html>
