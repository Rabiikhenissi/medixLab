<x-layouts.patient>
<x-slot:title>Évolution de ma santé — Medix eSanté</x-slot:title>

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
    .trend-card { transition: all 0.2s ease; }
    .trend-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .trend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .trend-up { color: #ef4444; }
    .trend-down { color: #3b82f6; }
    .trend-stable { color: #22c55e; }
    .stat-card { transition: all 0.15s ease; }
    .stat-card:hover { transform: translateY(-1px); }
</style>
@endsection

@section('content')
<div class="w-full max-w-[1100px] mx-auto py-8 px-4">

    <a href="{{ route('patient.dashboard') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#0D9488] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour
    </a>

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-[#0D9488]/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-[#1e293b]">Évolution de ma santé</h1>
            <p class="text-xs text-[#64748b] mt-0.5">Historique et tendances de vos analyses</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div id="summaryCards" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
        <div class="stat-card bg-white rounded-xl p-4 border border-[#e2e8f0]">
            <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider">Examens réalisés</p>
            <p id="totalResults" class="text-2xl font-black text-[#1e293b] mt-1">—</p>
        </div>
        <div class="stat-card bg-white rounded-xl p-4 border border-[#e2e8f0]">
            <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider">Taux d'achèvement</p>
            <p id="completionRate" class="text-2xl font-black text-[#0D9488] mt-1">—</p>
        </div>
        <div class="stat-card bg-white rounded-xl p-4 border border-[#e2e8f0]">
            <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider">Anomalies détectées</p>
            <p id="abnormalRate" class="text-2xl font-black text-amber-500 mt-1">—</p>
        </div>
        <div class="stat-card bg-white rounded-xl p-4 border border-[#e2e8f0]">
            <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider">Dernier examen</p>
            <p id="lastExam" class="text-lg font-bold text-[#1e293b] mt-1">—</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden mb-8">
        <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-gradient-to-br from-[#0D9488]/10 to-purple-500/10 blur-3xl pointer-events-none"></div>
        <h2 class="text-sm font-bold text-[#1e293b] mb-4">Graphiques des paramètres</h2>
        <p class="text-xs text-[#64748b] mb-6">Cliquez sur un paramètre pour afficher son évolution</p>

        <div id="chartContainer" class="relative" style="height:300px;">
            <canvas id="healthChart"></canvas>
        </div>
        <div id="noDataMsg" class="hidden py-12 text-center text-[#94a3b8]">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-sm font-semibold">Aucun résultat disponible</p>
            <p class="text-xs mt-1">Vos résultats apparaîtront ici une fois vos analyses complétées</p>
        </div>
    </div>

    {{-- Parameters List --}}
    <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-gradient-to-br from-blue-500/10 to-[#0D9488]/10 blur-3xl pointer-events-none"></div>
        <h2 class="text-sm font-bold text-[#1e293b] mb-4">Paramètres suivis</h2>
        <div id="paramsList" class="space-y-2">
            <div class="text-center py-8 text-[#94a3b8]">
                <div class="animate-spin w-5 h-5 border-2 border-[#0D9488] border-t-transparent rounded-full mx-auto mb-2"></div>
                <p class="text-xs">Chargement des données...</p>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    let healthChart = null;
    let allTrends = [];

    async function loadData() {
        try {
            const res = await fetch('{{ route("patient.health-trends-data") }}');
            const data = await res.json();
            if (!data.success) return;

            allTrends = data.trends;
            renderSummary(data.summary);
            renderParamsList(data.trends);

            if (data.trends.length > 0) {
                document.getElementById('chartContainer').classList.remove('hidden');
                document.getElementById('noDataMsg').classList.add('hidden');
                renderChart(data.trends[0]);
            } else {
                document.getElementById('chartContainer').classList.add('hidden');
                document.getElementById('noDataMsg').classList.remove('hidden');
            }
        } catch(e) {
            console.error('Failed to load health trends:', e);
        }
    }

    function renderSummary(s) {
        document.getElementById('totalResults').textContent = s.total_results;
        document.getElementById('completionRate').textContent = s.completion_rate + '%';
        document.getElementById('abnormalRate').textContent = s.abnormal_results + ' (' + s.abnormal_rate + '%)';
        document.getElementById('lastExam').textContent = s.last_exam_date || '—';
    }

    function renderParamsList(trends) {
        const el = document.getElementById('paramsList');
        if (trends.length === 0) {
            el.innerHTML = '<p class="text-xs text-[#94a3b8] text-center py-4">Aucun paramètre disponible</p>';
            return;
        }

        el.innerHTML = trends.map(t => {
            const stats = t.stats;
            const trendIcon = stats.trend === 'rising'
                ? '<span class="trend-up">↑ Hausse</span>'
                : (stats.trend === 'falling'
                    ? '<span class="trend-down">↓ Baisse</span>'
                    : '<span class="trend-stable">→ Stable</span>');
            const abnormalBadge = stats.abnormal_count > 0
                ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">' + stats.abnormal_count + ' anomalie(s)</span>'
                : '';

            return `
                <button type="button" onclick="selectParam('${t.parameter.replace(/'/g, "\\'")}')"
                    class="trend-card w-full text-left p-4 bg-white border border-[#e2e8f0] rounded-xl flex items-center justify-between gap-3 hover:border-[#0D9488]/30 transition cursor-pointer">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-[#1e293b]">${t.parameter}</span>
                            ${abnormalBadge}
                        </div>
                        <span class="text-[10px] text-[#94a3b8]">${t.unit ? t.unit + ' · ' : ''}${stats.count} mesure(s)</span>
                    </div>
                    <div class="text-right flex items-center gap-3">
                        <div>
                            <span class="text-sm font-black text-[#1e293b]">${stats.latest ?? '—'}</span>
                            <span class="text-[10px] text-[#94a3b8] block">dernière</span>
                        </div>
                        <div class="text-xs">${trendIcon}</div>
                    </div>
                </button>`;
        }).join('');
    }

    function selectParam(paramName) {
        const trend = allTrends.find(t => t.parameter === paramName);
        if (trend) renderChart(trend);
        document.getElementById('chartContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function renderChart(trend) {
        const ctx = document.getElementById('healthChart').getContext('2d');
        if (healthChart) healthChart.destroy();

        const labels = trend.data_points.map(d => d.date);
        const values = trend.data_points.map(d => d.value);
        const colors = trend.data_points.map(d =>
            d.status === 'high' || d.status === 'low' || d.status === 'abnormal'
                ? '#ef4444' : '#0D9488'
        );

        healthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: trend.parameter + (trend.unit ? ' (' + trend.unit + ')' : ''),
                    data: values,
                    borderColor: '#0D9488',
                    backgroundColor: 'rgba(13,148,136,0.08)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: colors,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' },
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 }, color: '#94a3b8' },
                    }
                }
            }
        });
    }

    loadData();
</script>
@endsection
</x-layouts.patient>
