<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>{{ __('center.billing.invoice', ['number' => $invoice->invoice_number]) }}</title>
<style>
    body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; margin: 20px; }
    .header { text-align: center; margin-bottom: 20px; }
    .header h1 { font-size: 18px; margin: 0; }
    .header p { margin: 2px 0; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 11px; }
    th { background: #f0f0f0; }
    .right { text-align: right; }
    .center { text-align: center; }
    .totals { margin-top: 15px; }
    .totals div { display: flex; justify-content: space-between; padding: 3px 0; }
    .bold { font-weight: bold; }
    .footer { margin-top: 30px; text-align: center; font-size: 10px; }
    @media print { body { margin: 0; } }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $invoice->labo->name }}</h1>
    <p>{{ $invoice->labo->address ?? '' }} | {{ $invoice->labo->phone ?? '' }}</p>
    <h2>{{ strtoupper(__('center.billing.invoice', ['number' => $invoice->invoice_number])) }}</h2>
    <p>{{ __('common.date') }}: {{ $invoice->created_at->format(__('center.date_format')) }}</p>
</div>

<p><strong>{{ __('doctor.patient_label') }}:</strong> {{ $invoice->patient->user->first_name }} {{ $invoice->patient->user->last_name }}</p>

<table>
    <thead><tr>
        <th>{{ __('center.billing.description') }}</th>
        <th class="center">{{ __('center.billing.qty') }}</th>
        <th class="right">{{ __('center.billing.unit_price') }}</th>
        <th class="right">{{ __('common.total') }}</th>
        <th class="right">{{ __('center.billing.cnam_col') }}</th>
    </tr></thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }} @if($item->cnam_code) ({{ $item->cnam_code }}) @endif</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->unit_price, 3) }}</td>
            <td class="right">{{ number_format($item->total, 3) }}</td>
            <td class="right">{{ number_format($item->cnam_coverage, 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <div><span>{{ __('common.total') }}</span><span>{{ number_format($invoice->total_amount, 3) }} TND</span></div>
    <div><span>{{ __('center.billing.cnam_part') }}</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
    <div class="bold"><span>{{ __('center.billing.patient_part') }}</span><span>{{ number_format($invoice->patient_amount, 3) }} TND</span></div>
    <div><span>{{ __('center.billing.paid') }}</span><span>{{ number_format($invoice->paid_amount, 3) }} TND</span></div>
    @if($invoice->balance > 0)
    <div class="bold"><span>{{ __('center.billing.remaining_to_pay') }}</span><span>{{ number_format($invoice->balance, 3) }} TND</span></div>
    @endif
</div>

<div class="footer">
    <p>{{ __('center.billing.generated_doc', ['brand' => 'Medix eSanté', 'date' => now()->format(__('common.datetime_format'))]) }}</p>
    <p>{{ __('center.billing.thanks') }}</p>
</div>
<script>window.print();</script>
</body>
</html>
