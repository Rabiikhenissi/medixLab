<x-layouts.patient>
<x-slot:title>Évolution de ma santé — Medix eSanté</x-slot:title>

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
    .stat-card { transition: all 0.15s ease; }
    .stat-card:hover { transform: translateY(-1px); }
    .param-chart-wrap { transition: all 0.3s ease; }
    .param-chart-wrap.open { max-height: 300px; opacity: 1; }
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

    {{-- Parameters List with Collapsible Charts --}}
    <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-gradient-to-br from-blue-500/10 to-[#0D9488]/10 blur-3xl pointer-events-none"></div>
        <h2 class="text-sm font-bold text-[#1e293b] mb-1">Paramètres suivis</h2>
        <p class="text-xs text-[#64748b] mb-5">Cliquez sur un paramètre pour voir son évolution</p>
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
    let allTrends = [];
    let chartInstances = {};

    async function loadData() {
        try {
            const res = await fetch('{{ route("patient.health-trends-data") }}');
            const data = await res.json();
            if (!data.success) return;
            allTrends = data.trends;
            renderSummary(data.summary);
            renderParamsList(data.trends);
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

        el.innerHTML = trends.map((t, idx) => {
            const stats = t.stats;
            const trendIcon = stats.trend === 'rising'
                ? '<span class="text-red-500 font-bold">↑ Hausse</span>'
                : (stats.trend === 'falling'
                    ? '<span class="text-blue-500 font-bold">↓ Baisse</span>'
                    : '<span class="text-emerald-500 font-bold">→ Stable</span>');
            const abnormalBadge = stats.abnormal_count > 0
                ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">' + stats.abnormal_count + ' anomalie(s)</span>'
                : '';

            return `
            <div class="bg-white border border-[#e2e8f0] rounded-xl overflow-hidden transition-all duration-200">
                <button type="button" onclick="toggleParam(${idx})"
                    class="param-trigger w-full text-left p-4 flex items-center justify-between gap-3 hover:bg-[#f8fafc] transition cursor-pointer">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-bold text-[#1e293b]">${escapeHtml(t.parameter)}</span>
                            ${abnormalBadge}
                        </div>
                        <div class="flex items-center gap-2 text-[10px] text-[#94a3b8] mt-0.5">
                            <span>${t.unit || '—'}</span>
                            <span>·</span>
                            <span>${stats.count} mesure(s)</span>
                            <span>·</span>
                            <span>Moy. ${stats.avg}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="text-right hidden sm:block">
                            <span class="text-sm font-black text-[#1e293b]">${stats.latest ?? '—'}</span>
                            <span class="text-[10px] text-[#94a3b8] block">dernière</span>
                        </div>
                        <div class="text-[11px] min-w-[60px] text-right">${trendIcon}</div>
                        <svg id="chevron-${idx}" class="w-4 h-4 text-[#94a3b8] transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div id="chartWrapper-${idx}" class="param-chart-wrap max-h-0 opacity-0 overflow-hidden transition-all duration-300" style="max-height:0;opacity:0;">
                    <div class="px-4 pb-4 pt-2 border-t border-[#e2e8f0]/60">
                        <div style="height:260px;position:relative;">
                            <canvas id="paramChart-${idx}"></canvas>
                        </div>
                        <div class="flex items-center justify-between mt-3 text-[10px] text-[#94a3b8]">
                            <span>${t.parameter} ${t.unit ? '(' + t.unit + ')' : ''}</span>
                            <span>Min: ${stats.min} · Max: ${stats.max} · Réf: ${t.data_points[0]?.reference_range || '—'}</span>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function toggleParam(idx) {
        const wrapper = document.getElementById('chartWrapper-' + idx);
        const chevron = document.getElementById('chevron-' + idx);
        const isOpen = wrapper.style.maxHeight !== '0px' && wrapper.style.maxHeight !== '';

        if (isOpen) {
            wrapper.style.maxHeight = '0';
            wrapper.style.opacity = '0';
            chevron.style.transform = 'rotate(0deg)';
            if (chartInstances[idx]) {
                chartInstances[idx].destroy();
                delete chartInstances[idx];
            }
        } else {
            wrapper.style.maxHeight = '340px';
            wrapper.style.opacity = '1';
            chevron.style.transform = 'rotate(180deg)';
            setTimeout(() => initChart(idx), 50);
        }
    }

    function initChart(idx) {
        if (chartInstances[idx]) return;
        const trend = allTrends[idx];
        if (!trend) return;

        const canvas = document.getElementById('paramChart-' + idx);
        if (!canvas) return;

        const labels = trend.data_points.map(d => d.date);
        const values = trend.data_points.map(d => d.value);
        const colors = trend.data_points.map(d =>
            d.status === 'high' || d.status === 'low' || d.status === 'abnormal'
                ? '#ef4444' : '#0D9488'
        );

        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(13,148,136,0.15)');
        gradient.addColorStop(1, 'rgba(13,148,136,0.01)');

        chartInstances[idx] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: trend.parameter + (trend.unit ? ' (' + trend.unit + ')' : ''),
                    data: values,
                    borderColor: '#0D9488',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: colors,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 12, weight: '700' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(item) {
                                return item.formattedValue + (trend.unit ? ' ' + trend.unit : '');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 45 }
                    },
                    y: {
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    }
                }
            }
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    loadData();
</script>
@endsection
</x-layouts.patient>