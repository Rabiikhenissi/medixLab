<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ __('patient.invoices.invoice_title', ['n' => $invoice->invoice_number]) }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12pt; color: #1e293b; padding: 30px; }
    .print-bar { position: sticky; top: 0; background: white; padding: 10px 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 10px; z-index: 100; }
    .print-bar button, .print-bar a { padding: 8px 20px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; text-decoration: none; color: #1e293b; }
    .print-bar button:hover, .print-bar a:hover { background: #e2e8f0; }
    .header { text-align: center; margin-bottom: 24px; }
    .header h1 { font-size: 18pt; color: #7C3AED; margin-bottom: 4px; }
    .header p { font-size: 10pt; color: #64748b; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
    .info-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; }
    .info-box h3 { font-size: 9pt; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
    .info-box p { font-size: 10pt; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #f8fafc; font-size: 9pt; color: #64748b; text-transform: uppercase; padding: 10px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; }
    td { padding: 8px; border-bottom: 1px solid #f1f5f9; font-size: 10pt; }
    td:last-child, th:last-child { text-align: right; }
    td:nth-child(2), th:nth-child(2) { text-align: center; }
    .totals { margin-left: auto; width: 300px; }
    .totals .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 10pt; }
    .totals .row.total { border-top: 2px solid #1e293b; padding-top: 10px; font-size: 14pt; font-weight: 700; margin-top: 6px; }
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 9pt; color: #94a3b8; text-align: center; }
    @media print { .print-bar { display: none; } body { padding: 0; } }
</style>
</head>
<body>
    <div class="print-bar">
        <button onclick="window.print()">{{ __('patient.invoices.print_pdf') }}</button>
        <a href="{{ route('patient.invoices.show', $invoice->id) }}">{{ __('common.back') }}</a>
    </div>

    <div class="header">
        <h1>Medix eSanté</h1>
        <p>{{ __('patient.invoices.invoice_title', ['n' => $invoice->invoice_number]) }}</p>
        <p>{{ __('patient.invoices.issued_on') }} {{ $invoice->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>{{ __('patient.invoices.laboratory') }}</h3>
            <p>{{ $invoice->labo->name ?? '-' }}</p>
            <p style="font-size:9pt;color:#64748b;">{{ $invoice->labo->address ?? '' }}</p>
        </div>
        <div class="info-box">
            <h3>{{ __('layout.role_patient') }}</h3>
            <p>{{ $invoice->patient->user->first_name ?? '' }} {{ $invoice->patient->user->last_name ?? '' }}</p>
            <p style="font-size:9pt;color:#64748b;">{{ __('patient.invoices.patient_code') }}: {{ $invoice->patient->patient_code ?? '' }}</p>
        </div>
    </div>

    <table>
        <thead><tr>
            <th>{{ __('patient.invoices.exam') }}</th>
            <th>{{ __('patient.invoices.qty') }}</th>
            <th>{{ __('patient.invoices.unit_price') }}</th>
            <th>{{ __('patient.invoices.total') }}</th>
        </tr></thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description ?? $item->exam->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 3) }} TND</td>
                <td>{{ number_format($item->total, 3) }} TND</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @if($invoice->cnam_amount > 0)
        <div class="row"><span>{{ __('patient.invoices.total') }}</span><span>{{ number_format($invoice->total_amount, 3) }} TND</span></div>
        <div class="row"><span>{{ __('patient.invoices.cnam_share') }}</span><span>- {{ number_format($invoice->cnam_amount, 3) }} TND</span></div>
        @endif
        <div class="row total"><span>{{ __('patient.invoices.net_to_pay') }}</span><span>{{ number_format($invoice->patient_amount, 3) }} TND</span></div>
        @if($invoice->paid_amount > 0)
        <div class="row" style="color:#059669;"><span>{{ __('patient.invoices.paid') }}</span><span>{{ number_format($invoice->paid_amount, 3) }} TND</span></div>
        @endif
    </div>

    <div class="footer">
        <p>{{ __('patient.invoices.auto_generated', ['brand' => 'Medix eSanté']) }} {{ now()->format(__('patient.invoices.date_time_format')) }}</p>
    </div>

    @if(request()->has('auto'))
    <script>window.print();</script>
    @endif
</body>
</html>
