<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Traite CNAM #{{ $invoice->invoice_number }}</title>
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
    <h1>TRAITE CNAM</h1>
    <p>N° {{ $invoice->invoice_number }}</p>
    <p>Date: {{ $invoice->created_at->format('d/m/Y') }}</p>
</div>

<div class="info">
    <div><span class="label">Laboratoire:</span><span>{{ $invoice->labo->name }}</span></div>
    <div><span class="label">Adresse:</span><span>{{ $invoice->labo->address ?? '' }}</span></div>
    <div><span class="label">Tél:</span><span>{{ $invoice->labo->phone ?? '' }}</span></div>
</div>

<div class="info">
    <div><span class="label">Patient:</span><span>{{ $invoice->patient->user->first_name }} {{ $invoice->patient->user->last_name }}</span></div>
    @if($invoice->patient->cnamAffiliation)
    <div><span class="label">N° CNAM:</span><span>{{ $invoice->patient->cnamAffiliation->cnam_number }}</span></div>
    <div><span class="label">N° Affiliation:</span><span>{{ $invoice->patient->cnamAffiliation->affiliation_number ?? '-' }}</span></div>
    <div><span class="label">Taux CNAM:</span><span>{{ $invoice->patient->cnamAffiliation->rate->taux ?? 0 }}% ({{ $invoice->patient->cnamAffiliation->rate->label ?? '' }})</span></div>
    @else
    <div><span class="label">CNAM:</span><span>Non affilié</span></div>
    @endif
</div>

<table>
    <thead><tr>
        <th>Code CNAM</th>
        <th>Acte</th>
        <th class="center">Qté</th>
        <th class="right">Valeur B</th>
        <th class="right">Coeff.</th>
        <th class="right">Montant</th>
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
    <div><span>Total actes</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
    <div class="bold"><span>Total à payer par CNAM</span><span>{{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
</div>

<div class="signature">
    <div><div class="line">Cachet et signature du laboratoire</div></div>
    <div><div class="line">Cachet et signature du patient</div></div>
</div>

<div class="footer">
    <p>Document généré par Medix eSanté le {{ now()->format('d/m/Y H:i') }}</p>
</div>
<script>window.print();</script>
</body>
</html>
