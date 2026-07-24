<x-layouts.patient>
    <x-slot:title>Mes Statistiques - Medix eSanté</x-slot:title>
    @section('content')

    <div class="w-full max-w-[1200px] mx-auto py-8 px-4">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8 select-none">
            <div class="flex items-center gap-4">
                <a href="{{ route('patient.dashboard') }}" class="w-10 h-10 bg-[#f1f5f9] hover:bg-[#e2e8f0] rounded-xl flex items-center justify-center text-[#64748b] transition" title="Retour">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-[#1e293b]">
                        Mes <span class="text-[#0D9488]">Statistiques</span>
                    </h1>
                    <p class="text-sm text-[#64748b] mt-1">Suivi de votre activité médicale</p>
                </div>
            </div>
            <a href="{{ route('patient.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 hover:bg-[#0D9488] hover:text-white transition uppercase tracking-wider">
                Tableau de bord
            </a>
        </div>

        @php
            $totalExams = array_sum($statusCounts);
            $completedExams = $statusCounts['completed'] ?? 0;
            $pendingExams = ($statusCounts['pending'] ?? 0) + ($statusCounts['assigned'] ?? 0);
            $cancelledExams = $statusCounts['cancelled'] ?? 0;
            $completionRate = $totalExams > 0 ? round(($completedExams / $totalExams) * 100) : 0;
        @endphp

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-[#0D9488]/5 to-[#0D9488]/10 border border-[#0D9488]/20 rounded-2xl p-5">
                <div class="text-3xl font-black text-[#0D9488]">{{ $totalExams }}</div>
                <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mt-1">Total Demandes</p>
            </div>
            <div class="bg-gradient-to-br from-green-500/5 to-green-500/10 border border-green-500/20 rounded-2xl p-5">
                <div class="text-3xl font-black text-green-600">{{ $completedExams }}</div>
                <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mt-1">Complétées</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500/5 to-amber-500/10 border border-amber-500/20 rounded-2xl p-5">
                <div class="text-3xl font-black text-amber-600">{{ $pendingExams }}</div>
                <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mt-1">En Cours</p>
            </div>
            <div class="bg-gradient-to-br from-red-500/5 to-red-500/10 border border-red-500/20 rounded-2xl p-5">
                <div class="text-3xl font-black text-red-600">{{ $cancelledExams }}</div>
                <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mt-1">Annulées</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Monthly Chart -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/>
                    </svg>
                    Évolution Mensuelle
                </h3>
                <div style="position:relative; height:250px; width:100%;">
                    <canvas id="patientMonthlyChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                    </svg>
                    Répartition par Statut
                </h3>
                <div style="position:relative; height:250px; width:100%; display:flex; justify-content:center;">
                    <canvas id="patientStatusChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Completion Rate Bar -->
        <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4">Taux de Complétion</h3>
            <div class="w-full bg-[#f1f5f9] rounded-full h-4 overflow-hidden">
                <div class="h-4 rounded-full bg-gradient-to-r from-[#0D9488] to-green-400 transition-all duration-700"
                     style="width: {{ $completionRate }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs font-semibold text-[#64748b]">
                <span>{{ $completedExams }} complétée(s)</span>
                <span>{{ $completionRate }}%</span>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);

            // Monthly bar chart
            new Chart(document.getElementById('patientMonthlyChart'), {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.label),
                    datasets: [{
                        label: 'Demandes',
                        data: chartData.map(d => d.count),
                        backgroundColor: 'rgba(13, 148, 136, 0.2)',
                        borderColor: '#0D9488',
                        borderWidth: 2,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } }, grid: { color: '#f1f5f9' } }
                    }
                }
            });

            // Status doughnut chart
            const statusCounts = @json($statusCounts);
            new Chart(document.getElementById('patientStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Assignée', 'Collectée', 'En traitement', 'Complétée', 'Annulée'],
                    datasets: [{
                        data: [
                            statusCounts.pending || 0,
                            statusCounts.assigned || 0,
                            statusCounts.collected || 0,
                            statusCounts.processing || 0,
                            statusCounts.completed || 0,
                            statusCounts.cancelled || 0
                        ],
                        backgroundColor: ['#fbbf24', '#14b8a6', '#3b82f6', '#a855f7', '#22c55e', '#ef4444'],
                        borderWidth: 1.5,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { font: { family: 'Inter', size: 11 }, boxWidth: 12 }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
    @endsection
</x-layouts.patient>
