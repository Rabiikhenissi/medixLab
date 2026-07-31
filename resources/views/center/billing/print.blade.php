<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Facture #{{ $invoice->invoice_number }}</title>
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
    <h2>FACTURE #{{ $invoice->invoice_number }}</h2>
    <p>Date: {{ $invoice->created_at->format('d/m/Y') }}</p>
</div>

<p><strong>Patient:</strong> {{ $invoice->patient->user->first_name }} {{ $invoice->patient->user->last_name }}</p>

<table>
    <thead><tr>
        <th>Description</th>
        <th class="center">Qté</th>
        <th class="right">Prix unit.</th>
        <th class="right">Total</th>
        <th class="right">CNAM</th>
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
    <div><span>Total</span><span>{{ number_format($invoice->total_amount, 3) }} TND</span></div>
    <div><span>Part CNAM</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
    <div class="bold"><span>Part Patient</span><span>{{ number_format($invoice->patient_amount, 3) }} TND</span></div>
    <div><span>Payé</span><span>{{ number_format($invoice->paid_amount, 3) }} TND</span></div>
    @if($invoice->balance > 0)
    <div class="bold"><span>Reste à payer</span><span>{{ number_format($invoice->balance, 3) }} TND</span></div>
    @endif
</div>

<div class="footer">
    <p>Medix eSanté - Document généré le {{ now()->format('d/m/Y H:i') }}</p>
    <p>Merci de votre confiance.</p>
</div>
<script>window.print();</script>
</body>
</html>
