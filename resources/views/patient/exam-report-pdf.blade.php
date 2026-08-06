<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('patient.report.title') }} — Medix eSanté</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #0D9488; padding-bottom: 12px; margin-bottom: 14px; }
        .brand { font-size: 18px; font-weight: bold; color: #1e293b; }
        .brand-sub { font-size: 10px; color: #64748b; }
        .meta { text-align: right; font-size: 10px; color: #64748b; }
        .meta .ref { font-size: 14px; font-weight: bold; color: #0D9488; }
        .section { font-size: 9px; font-weight: bold; letter-spacing: 0.1em; text-transform: uppercase; color: #64748b; margin: 16px 0 6px; }
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.info td { width: 50%; border: 1px solid #e2e8f0; padding: 8px 10px; vertical-align: top; }
        .label { font-size: 9px; color: #94a3b8; font-weight: bold; text-transform: uppercase; }
        .value { font-size: 13px; font-weight: bold; color: #1e293b; }
        .sub { font-size: 10px; color: #64748b; }
        .notes { border: 1px solid #fde68a; background: #fffbeb; padding: 8px 10px; font-style: italic; color: #92400e; font-size: 11px; }
        table.results { width: 100%; border-collapse: collapse; margin: 6px 0 4px; }
        table.results th { background: #f1f5f9; padding: 6px 10px; font-size: 9px; text-align: left; text-transform: uppercase; color: #64748b; }
        table.results td { padding: 7px 10px; border-top: 1px solid #f1f5f9; font-size: 11px; }
        .normal { color: #16a34a; font-weight: bold; }
        .high { color: #dc2626; font-weight: bold; }
        .low { color: #d97706; font-weight: bold; }
        .critical { color: #7f1d1d; font-weight: bold; background: #fecaca; }
        .interp { padding: 6px 10px; font-size: 10px; font-style: italic; color: #475569; }
        .doctor-interp { border: 1px solid #ddd6fe; background: #f5f3ff; padding: 10px; margin-top: 12px; font-size: 12px; color: #374151; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
        .signature { text-align: right; margin-top: 28px; }
        .sign-line { width: 180px; border-top: 1px solid #cbd5e1; margin-top: 2px; margin-left: auto; margin-bottom: 4px; }
        .no-results { padding: 8px 10px; color: #94a3b8; font-style: italic; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:60%; vertical-align:top;">
                    <div class="brand">Medix eSanté</div>
                    <div class="brand-sub">{{ __('patient.report.official_subtitle') }}</div>
                </td>
                <td class="meta" style="vertical-align:top;">
                    <div class="ref">{{ __('patient.report.ref_prefix') }} #{{ $examRequest->id }}</div>
                    <div>{{ __('patient.report.issued_on') }} {{ now()->format(__('patient.report.date_time_format')) }}</div>
                    @if($examRequest->laboratory)
                        <div style="font-weight:bold;color:#1e293b;margin-top:4px;">
                            {{ $examRequest->laboratory->name }}
                            @if($examRequest->laboratory->city) — {{ $examRequest->laboratory->city }}@endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section">{{ __('patient.report.patient_info') }}</div>
    <table class="info">
        <tr>
            <td>
                <div class="label">{{ __('layout.role_patient') }}</div>
                <div class="value">{{ $examRequest->patient->user->first_name }} {{ $examRequest->patient->user->last_name }}</div>
                <div class="sub">{{ __('patient.invoices.patient_code') }} : {{ $examRequest->patient->patient_code }}</div>
            </td>
            <td>
                <div class="label">{{ __('patient.report.prescribing_doctor') }}</div>
                <div class="value">Dr. {{ $examRequest->doctor->user->first_name }} {{ $examRequest->doctor->user->last_name }}</div>
                <div class="sub">{{ $examRequest->doctor->speciality }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">{{ __('patient.report.prescription_date') }}</div>
                <div class="value">{{ $examRequest->created_at->format(__('patient.report.date_time_format')) }}</div>
            </td>
            <td>
                <div class="label">{{ __('common.status') }}</div>
                <div class="value">{{ __('patient.report.completed_approved') }}</div>
            </td>
        </tr>
    </table>

    @if($examRequest->clinical_notes)
        <div class="section">{{ __('patient.dashboard.clinical_notes') }}</div>
        <div class="notes">"{{ $examRequest->clinical_notes }}"</div>
    @endif

    <div class="section">{{ __('patient.report.results_count', ['n' => $examRequest->items->count()]) }}</div>

    @foreach($examRequest->items as $item)
        <table class="results">
            <tr>
                <th colspan="4">{{ $item->exam->name }}@if($item->exam->category) — {{ $item->exam->category }}@endif</th>
            </tr>
            @if($item->resultLabo && $item->resultLabo->details->count() > 0)
                <tr>
                    <th style="width:38%;">{{ __('patient.report.parameter') }}</th>
                    <th style="width:18%;">{{ __('patient.report.value') }}</th>
                    <th style="width:28%;">{{ __('patient.report.normal_range') }}</th>
                    <th style="width:16%;">{{ __('common.status') }}</th>
                </tr>
                @foreach($item->resultLabo->details as $detail)
                    <tr>
                        <td style="font-weight:bold;">{{ $detail->parameter }}@if($detail->unit) <span style="font-size:9px;color:#7C3AED;">({{ $detail->unit }})</span>@endif</td>
                        <td style="font-weight:bold;">{{ $detail->value }}</td>
                        <td style="color:#64748b;">{{ $detail->reference_range ?? '—' }}</td>
                        <td>
                            @php
                                $map = ['normal' => 'normal', 'high' => 'high', 'low' => 'low', 'critical' => 'critical'];
                                $cls = $map[$detail->status] ?? 'normal';
                                $label = match ($detail->status) {
                                    'normal' => '✓ ' . __('patient.report.status_normal'),
                                    'high' => '↑ ' . __('patient.report.status_high'),
                                    'low' => '↓ ' . __('patient.report.status_low'),
                                    'critical' => '⚠ ' . __('patient.report.status_critical'),
                                    default => $detail->status,
                                };
                            @endphp
                            <span class="{{ $cls }}">{{ $label }}</span>
                        </td>
                    </tr>
                @endforeach
                @if($item->resultLabo->interpretation)
                    <tr>
                        <td colspan="4" class="interp">{{ __('patient.report.interpretation') }} {{ $item->resultLabo->interpretation }}</td>
                    </tr>
                @endif
            @else
                <tr>
                    <td class="no-results">{{ __('patient.report.no_detailed_results') }}</td>
                </tr>
            @endif
        </table>
    @endforeach

    @if($examRequest->doctor_interpretation)
        <div class="doctor-interp">
            <div class="section" style="margin-top:0; color:#6d28d9;">{{ __('patient.report.doctor_interpretation') }}</div>
            {{ $examRequest->doctor_interpretation }}
        </div>
    @endif

    <div class="footer">
        Medix eSanté — {{ __('patient.report.digital_health_platform') }} — {{ __('patient.report.auto_generated') }} {{ now()->format(__('patient.report.date_time_format')) }}
    </div>

    <div class="signature">
        <div class="sign-line"></div>
        <div style="font-size:11px;color:#64748b;">
            Dr. {{ $examRequest->doctor->user->first_name }} {{ $examRequest->doctor->user->last_name }}<br>
            <span style="font-weight:bold;">{{ $examRequest->doctor->speciality }}</span>
        </div>
    </div>

</body>
</html>
