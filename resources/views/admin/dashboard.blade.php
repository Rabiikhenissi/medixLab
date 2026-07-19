@extends('layouts.admin')

@section('title', 'Espace Administrateur')


@section('page-title')
Espace <span style="color:#0066ff;">Administrateur</span>
@endsection


@section('page-subtitle')
Gérez la plateforme et supervisez les activités de Medix eSanté.
@endsection

<!-- Import Chart.js (Task 3.1) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom CSS overrides for stats-grid and charts -->
<style>
    .stats-grid-6 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }
    @media (max-width: 1024px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
    .chart-container {
        background: #ffffff;
        border: 1px solid #e8eef4;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .top-exams-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .top-exam-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .top-exam-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
    }
    .top-exam-count {
        font-weight: 800;
        color: #0066ff;
        background: #0066ff/10;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>

<!-- ================= STATS (Task 3.1) ================= -->
<div class="stats-grid-6">

    <!-- Patients -->
    <div class="stat-card anim anim-1">
        <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Patients</div>
            <div class="stat-value">{{ $stats['total_patients'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Doctors -->
    <div class="stat-card anim anim-1">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Médecins</div>
            <div class="stat-value">{{ $stats['total_doctors'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Laboratories -->
    <div class="stat-card anim anim-2">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Centres/Labos</div>
            <div class="stat-value">{{ $stats['total_laboratories'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Prescriptions -->
    <div class="stat-card anim anim-2">
        <div class="stat-icon" style="background:#f0fdf4; color:#15803d">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Prescriptions</div>
            <div class="stat-value">{{ $stats['total_exam_requests'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Examens actifs -->
    <div class="stat-card anim anim-3">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.3 24.3 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Examens Actifs</div>
            <div class="stat-value">{{ $stats['total_exams'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Archives -->
    <div class="stat-card anim anim-3">
        <div class="stat-icon orange">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5" />
            </svg>
        </div>
        <div>
            <div class="stat-label">Archivés</div>
            <div class="stat-value">{{ $stats['archived_exams'] ?? 0 }}</div>
        </div>
    </div>

</div>

<!-- ================= CHARTS AND TRENDS (Task 3.1) ================= -->
<div class="charts-grid anim anim-4">

    <!-- Trend line chart -->
    <div class="chart-container">
        <h3 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:16px; text-transform:uppercase; letter-spacing:0.04em;">
            Volume des prescriptions (15 derniers jours)
        </h3>
        <div style="position:relative; height:280px; width:100%;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Status distribution chart -->
    <div class="chart-container">
        <h3 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:16px; text-transform:uppercase; letter-spacing:0.04em;">
            Statuts des prescriptions
        </h3>
        <div style="position:relative; height:220px; width:100%; display:flex; justify-content:center;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

<!-- ================= MOST PRESCRIBED & ACTIONS (Task 3.1) ================= -->
<div class="charts-grid anim anim-4" style="margin-top:24px;">

    <!-- Top Prescribed Exams -->
    <div class="chart-container">
        <h3 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:20px; text-transform:uppercase; letter-spacing:0.04em;">
            🏆 Top 5 des examens les plus prescrits
        </h3>
        @if($topExams->count() > 0)
            <div class="top-exams-list">
                @foreach($topExams as $index => $item)
                    <div class="top-exam-item">
                        <div class="flex items-center gap-3">
                            <span style="font-weight:900; color:#64748b; font-size:14px;">#{{ $index + 1 }}</span>
                            <span class="top-exam-name">{{ $item->exam->name }}</span>
                        </div>
                        <span class="top-exam-count">{{ $item->count }} prescription(s)</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:13px; color:#64748b; font-style:italic;">Aucune prescription enregistrée.</p>
        @endif
    </div>

    <!-- Quick Links / Modules -->
    <div class="chart-container">
        <h3 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:20px; text-transform:uppercase; letter-spacing:0.04em;">
            ⚡ Accès aux modules
        </h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.exams.index') }}" style="display:flex; align-items:center; justify-content:space-between; padding:12px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; color:inherit; font-weight:600; font-size:13px;" class="hover:bg-slate-50 transition">
                <span>🧪 Gestion des examens</span>
                <span style="color:#0066ff;">→</span>
            </a>
            <a href="{{ route('admin.laboratories.index') }}" style="display:flex; align-items:center; justify-content:space-between; padding:12px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; color:inherit; font-weight:600; font-size:13px;" class="hover:bg-slate-50 transition">
                <span>🏥 Gestion des établissements</span>
                <span style="color:#0066ff;">→</span>
            </a>
            <a href="{{ route('admin.users.index') }}" style="display:flex; align-items:center; justify-content:space-between; padding:12px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; color:inherit; font-weight:600; font-size:13px;" class="hover:bg-slate-50 transition">
                <span>👥 Gestion des utilisateurs</span>
                <span style="color:#0066ff;">→</span>
            </a>
        </div>
    </div>

</div>

<!-- Render Javascript Charts (Task 3.1) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Line Chart: Daily Volume Trend
        const chartData = @json($chartData);
        const labels = chartData.map(d => d.label);
        const counts = chartData.map(d => d.count);

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Volume de prescriptions',
                    data: counts,
                    borderColor: '#0066ff',
                    backgroundColor: 'rgba(0, 102, 255, 0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0066ff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            font: { family: 'Inter', size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

        // 2. Doughnut Chart: Status distribution
        const statusDistribution = @json($statusDistribution);
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Labo choisi', 'Collecté', 'En cours', 'Complété', 'Annulé'],
                datasets: [{
                    data: [
                        statusDistribution.pending,
                        statusDistribution.assigned,
                        statusDistribution.collected,
                        statusDistribution.processing,
                        statusDistribution.completed,
                        statusDistribution.cancelled
                    ],
                    backgroundColor: [
                        '#fbbf24', // pending: amber
                        '#14b8a6', // assigned: teal
                        '#3b82f6', // collected: blue
                        '#a855f7', // processing: purple
                        '#22c55e', // completed: green
                        '#ef4444'  // cancelled: red
                    ],
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
                        labels: {
                            font: { family: 'Inter', size: 11 },
                            boxWidth: 12
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>

@endsection