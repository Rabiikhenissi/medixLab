<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Code-barres - {{ $sample->sample_code }}</title>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<style>
    body { display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; }
    .label { text-align: center; padding: 20px; }
    .label h2 { margin: 10px 0 5px; font-size: 14px; }
    .label p { margin: 2px 0; font-size: 11px; color: #555; }
    .label .code { font-family: monospace; font-size: 16px; font-weight: bold; margin-top: 5px; letter-spacing: 1px; }
    @media print { body { padding: 0; } @page { margin: 0.5cm; } }
</style>
</head>
<body>
<div class="label">
    <h2>{{ $sample->patient->user->first_name }} {{ $sample->patient->user->last_name }}</h2>
    <p>{{ $sample->examRequestItem->exam->name ?? 'Examen' }} · {{ $sample->material_type ?? '' }}</p>
    <svg id="barcode"></svg>
    <div class="code">{{ $sample->sample_code }}</div>
    <p style="margin-top:8px;font-size:9px;">{{ $sample->labo->name ?? '' }}</p>
</div>
<script>JsBarcode('#barcode', '{{ $sample->sample_code }}', { format:'CODE128', width:1.8, height:60, displayValue:false });</script>
<script>window.print();</script>
</body>
</html>
