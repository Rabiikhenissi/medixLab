<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'Analyses — Medix eSanté</title>
    <style>
        /* ── Google Font (loaded inline so it works offline too via print) ── */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            background: #fff;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── Screen Wrapper ── */
        .page-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        /* ── Header ── */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #0D9488;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-logo {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #0D9488, #0a7068);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-logo svg { width: 24px; height: 24px; color: #fff; }
        .brand-name { font-size: 20px; font-weight: 900; color: #1e293b; }
        .brand-sub  { font-size: 11px; color: #64748b; font-weight: 600; margin-top: 1px; }
        .report-meta { text-align: right; font-size: 11px; color: #64748b; }
        .report-meta .ref { font-size: 15px; font-weight: 900; color: #0D9488; }

        /* ── Section title ── */
        .section-title {
            font-size: 10px; font-weight: 800; letter-spacing: 0.08em;
            text-transform: uppercase; color: #64748b;
            margin-bottom: 8px; margin-top: 20px;
        }

        /* ── Info grid ── */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-box {
            background: #F8FAFC; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 12px 14px;
        }
        .info-box .label { font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
        .info-box .value { font-size: 14px; font-weight: 700; color: #1e293b; }
        .info-box .sub   { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 99px;
            font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
            background: #dcfce7; color: #15803d; border: 1.5px solid #86efac;
        }

        /* ── Clinical notes ── */
        .clinical-notes {
            background: #fffbeb; border: 1px solid #fde68a;
            border-radius: 10px; padding: 12px 14px;
            font-style: italic; color: #92400e; font-size: 12px;
        }

        /* ── Exam cards ── */
        .exam-card {
            border: 1px solid #e2e8f0; border-radius: 10px;
            margin-bottom: 12px; overflow: hidden;
        }
        .exam-card-header {
            background: #F8FAFC; padding: 10px 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .exam-dot { width: 8px; height: 8px; border-radius: 50%; background: #0D9488; flex-shrink: 0; }
        .exam-name { font-size: 13px; font-weight: 700; color: #1e293b; }
        .exam-cat  { font-size: 10px; color: #0D9488; font-weight: 600; margin-left: auto; }

        .results-table { width: 100%; border-collapse: collapse; }
        .results-table th {
            background: #f1f5f9; padding: 7px 14px;
            font-size: 10px; font-weight: 700; text-align: left;
            color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .results-table td { padding: 8px 14px; font-size: 12px; border-top: 1px solid #f1f5f9; }
        .results-table tr:hover td { background: #fafafa; }
        .status-normal { color: #16a34a; font-weight: 700; }
        .status-high   { color: #dc2626; font-weight: 700; }
        .status-low    { color: #d97706; font-weight: 700; }

        .interp {
            padding: 8px 14px; font-size: 11px; font-style: italic;
            color: #475569; border-top: 1px dashed #e2e8f0;
        }

        /* ── Doctor interpretation ── */
        .doctor-interp {
            background: #f5f3ff; border: 1px solid #ddd6fe;
            border-radius: 10px; padding: 12px 14px; margin-top: 16px;
        }

        /* ── Footer ── */
        .report-footer {
            margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 10px; color: #94a3b8;
        }
        .signature-box { text-align: right; }
        .signature-line { width: 160px; border-top: 1px solid #cbd5e1; margin-top: 32px; margin-left: auto; margin-bottom: 4px; }

        /* ── Print button (screen only) ── */
        .print-bar {
            position: sticky; top: 0; z-index: 100;
            background: #0D9488; color: #fff;
            padding: 10px 24px; display: flex; justify-content: space-between; align-items: center;
        }
        .print-bar p { font-size: 12px; font-weight: 600; }
        .print-btn {
            display: inline-flex; align-items: center; gap-x: 6px;
            background: #fff; color: #0D9488;
            font-size: 12px; font-weight: 800;
            padding: 7px 18px; border-radius: 8px; border: none; cursor: pointer;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .close-btn {
            display: inline-flex; align-items: center; gap-x: 6px;
            background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,0.4);
            font-size: 12px; font-weight: 700;
            padding: 7px 16px; border-radius: 8px; cursor: pointer;
            text-transform: uppercase; letter-spacing: 0.05em;
        }

        /* ── Print media ── */
        @media print {
            .print-bar { display: none !important; }
            body { font-size: 12px; }
            .page-wrapper { padding: 0; }
            .exam-card { break-inside: avoid; }
        }
    </style>
</head>
<body>

{{-- Print / close bar (hidden during actual print) --}}
<div class="print-bar">
    <p>📋 Rapport d'Analyses — Medix eSanté</p>
    <div style="display:flex;gap:8px">
        <button class="close-btn" onclick="history.back()">← Retour</button>
        <button class="print-btn" onclick="window.print()">🖨 Imprimer / PDF</button>
    </div>
</div>

<div class="page-wrapper">

    {{-- ─── Header ───────────────────────────────────────────────────── --}}
    <div class="report-header">
        <div class="brand">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#fff">
                    <path d="M4 20V8l8 7 8-7v12"/><path d="M12 3v4M10 5h4"/>
                </svg>
            </div>
            <div>
                <div class="brand-name">Medix eSanté</div>
                <div class="brand-sub">Rapport officiel d'analyses médicales</div>
            </div>
        </div>
        <div class="report-meta">
            <div class="ref">Réf. #{{ $examRequest->id }}</div>
            <div>Émis le {{ now()->format('d/m/Y à H:i') }}</div>
            @if($examRequest->laboratory)
            <div style="margin-top:4px; font-weight:700; color:#1e293b">
                {{ $examRequest->laboratory->name }}
                @if($examRequest->laboratory->city)— {{ $examRequest->laboratory->city }}@endif
            </div>
            @endif
        </div>
    </div>

    {{-- ─── Patient / Doctor info ─────────────────────────────────────── --}}
    <p class="section-title">Informations du patient</p>
    <div class="info-grid">
        <div class="info-box">
            <div class="label">Patient</div>
            <div class="value">{{ $examRequest->patient->user->first_name }} {{ $examRequest->patient->user->last_name }}</div>
            <div class="sub">Code : {{ $examRequest->patient->patient_code }}</div>
        </div>
        <div class="info-box">
            <div class="label">Médecin prescripteur</div>
            <div class="value">Dr. {{ $examRequest->doctor->user->first_name }} {{ $examRequest->doctor->user->last_name }}</div>
            <div class="sub">{{ $examRequest->doctor->speciality }}</div>
        </div>
        <div class="info-box">
            <div class="label">Date de prescription</div>
            <div class="value">{{ $examRequest->created_at->format('d/m/Y') }}</div>
            <div class="sub">{{ $examRequest->created_at->format('H:i') }}</div>
        </div>
        <div class="info-box">
            <div class="label">Statut</div>
            <div class="value">
                <span class="status-badge">✓ Complétée & Approuvée</span>
            </div>
        </div>
    </div>

    {{-- Clinical notes --}}
    @if($examRequest->clinical_notes)
    <p class="section-title">Notes cliniques</p>
    <div class="clinical-notes">"{{ $examRequest->clinical_notes }}"</div>
    @endif

    {{-- ─── Exam results ───────────────────────────────────────────────── --}}
    <p class="section-title">Résultats des analyses ({{ $examRequest->items->count() }} examen(s))</p>

    @foreach($examRequest->items as $item)
    <div class="exam-card">
        <div class="exam-card-header">
            <div class="exam-dot"></div>
            <span class="exam-name">{{ $item->exam->name }}</span>
            @if($item->exam->category)
            <span class="exam-cat">{{ $item->exam->category }}</span>
            @endif
        </div>

        @if($item->resultLabo && $item->resultLabo->details->count() > 0)
        <table class="results-table">
            <thead>
                <tr>
                    <th>Paramètre</th>
                    <th>Valeur</th>
                    <th>Plage normale</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->resultLabo->details as $detail)
                <tr>
                    <td style="font-weight:600">{{ $detail->parameter }}</td>
                    <td style="font-weight:700">{{ $detail->value }}</td>
                    <td style="color:#94a3b8">{{ $detail->reference_range ?? '—' }}</td>
                    <td>
                        @php $smap = ['normal'=>'status-normal','high'=>'status-high','low'=>'status-low']; @endphp
                        <span class="{{ $smap[$detail->status] ?? '' }}">
                            @if($detail->status === 'normal') ✓ Normal
                            @elseif($detail->status === 'high') ↑ Élevé
                            @elseif($detail->status === 'low')  ↓ Bas
                            @else {{ $detail->status }}
                            @endif
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($item->resultLabo->interpretation)
        <p class="interp">Interprétation : {{ $item->resultLabo->interpretation }}</p>
        @endif
        @else
        <p style="padding:10px 14px; color:#94a3b8; font-style:italic; font-size:12px">Aucun résultat détaillé disponible.</p>
        @endif
    </div>
    @endforeach

    {{-- ─── Doctor interpretation ──────────────────────────────────────── --}}
    @if($examRequest->doctor_interpretation)
    <div class="doctor-interp">
        <p class="section-title" style="margin-top:0; color:#6d28d9">Interprétation du médecin</p>
        <p style="font-size:13px; color:#374151">{{ $examRequest->doctor_interpretation }}</p>
    </div>
    @endif

    {{-- ─── Footer / Signature ─────────────────────────────────────────── --}}
    <div class="report-footer">
        <div>
            <div>Medix eSanté — Plateforme de santé numérique</div>
            <div style="margin-top:2px">Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div style="font-size:11px; color:#64748b">
                Dr. {{ $examRequest->doctor->user->first_name }} {{ $examRequest->doctor->user->last_name }}<br>
                <span style="font-weight:600">{{ $examRequest->doctor->speciality }}</span>
            </div>
        </div>
    </div>

</div>

<script>
    // Auto-open print dialog if ?auto=1 is passed
    if (new URLSearchParams(window.location.search).get('auto') === '1') {
        window.addEventListener('load', () => window.print());
    }
</script>

</body>
</html>
