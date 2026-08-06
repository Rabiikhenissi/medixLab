@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))


@section('page-title')
<span style="color:#0066ff;">{{ __('admin.dashboard.title') }}</span>
@endsection


@section('page-subtitle')
{{ __('admin.dashboard.page_subtitle') }}
@endsection

@section('content')

<!-- Import Chart.js (Task 3.1) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom CSS overrides for stats-grid and charts -->
<style>
    .stats-grid-6 {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }
    @media (max-width: 1200px) {
        .stats-grid-6 {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stats-grid-6 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    .charts-grid-wide {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .charts-grid, .charts-grid-wide {
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
    .chart-container h3 {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 16px 0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .top-exams-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .top-exam-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
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
        background: rgba(0,102,255,0.08);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
    }
    .module-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none;
        color: inherit;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.15s;
    }
    .module-link:hover {
        background: #f8fafc;
        border-color: #0066ff;
    }
    .module-link span:last-child {
        color: #0066ff;
        font-weight: 700;
    }
    .recent-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .recent-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .recent-item-avatar {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        color: white;
        flex-shrink: 0;
    }
    .recent-item-info {
        flex: 1;
        min-width: 0;
    }
    .recent-item-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .recent-item-sub {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .status-pill .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }
    .pill-pending { background: #fffbeb; color: #d97706; }
    .pill-pending .dot { background: #f59e0b; }
    .pill-assigned { background: #f0fdfa; color: #0d9488; }
    .pill-assigned .dot { background: #14b8a6; }
    .pill-collected { background: #eff6ff; color: #2563eb; }
    .pill-collected .dot { background: #3b82f6; }
    .pill-processing { background: #faf5ff; color: #7c3aed; }
    .pill-processing .dot { background: #a855f7; }
    .pill-completed { background: #f0fdf4; color: #16a34a; }
    .pill-completed .dot { background: #22c55e; }
    .pill-cancelled { background: #fef2f2; color: #dc2626; }
    .pill-cancelled .dot { background: #ef4444; }
    .city-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
    }
    .city-bar-name {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        width: 100px;
        flex-shrink: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .city-bar-track {
        flex: 1;
        height: 8px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
    }
    .city-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #0066ff, #00aaff);
        border-radius: 4px;
        transition: width 0.6s ease;
    }
    .city-bar-count {
        font-size: 12px;
        font-weight: 800;
        color: #0066ff;
        width: 28px;
        text-align: right;
        flex-shrink: 0;
    }
    .summary-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        text-align: center;
    }
    .summary-card-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .summary-card-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-top: 4px;
    }
</style>

<!-- ================= STATS ================= -->
<div class="stats-grid-6">

    <!-- Patients -->
    <div class="stat-card anim anim-1">
        <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
            </svg>
        </div>
        <div>
            <div class="stat-label">{{ __('admin.dashboard.patients') }}</div>
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
            <div class="stat-label">{{ __('admin.dashboard.doctors') }}</div>
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
            <div class="stat-label">{{ __('admin.dashboard.centers_labs') }}</div>
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
            <div class="stat-label">{{ __('admin.dashboard.prescriptions') }}</div>
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
            <div class="stat-label">{{ __('admin.dashboard.exams_active') }}</div>
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
            <div class="stat-label">{{ __('admin.dashboard.archive_stat') }}</div>
            <div class="stat-value">{{ $stats['archived_exams'] ?? 0 }}</div>
        </div>
    </div>

</div>

<!-- ================= CHARTS ================= -->
<div class="charts-grid anim anim-4">

    <!-- Trend line chart -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.prescription_trend') }}</h3>
        <div style="position:relative; height:260px; width:100%;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Status distribution chart -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.prescription_status') }}</h3>
        <div style="position:relative; height:260px; width:100%; display:flex; justify-content:center;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

<!-- ================= TOP EXAMS & MODULES ================= -->
<div class="charts-grid-wide anim anim-4">

    <!-- Top Prescribed Exams -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.top_exams') }}</h3>
        @if(count($topExams) > 0)
            <div class="top-exams-list">
                @foreach($topExams as $index => $item)
                    <div class="top-exam-item">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="font-weight:900; color:#64748b; font-size:14px;">#{{ $index + 1 }}</span>
                            <span class="top-exam-name">{{ $item['name'] }}</span>
                        </div>
                        <span class="top-exam-count">{{ __('admin.dashboard.prescription_count', ['count' => $item['count']]) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:13px; color:#64748b; font-style:italic; margin:0;">{{ __('admin.dashboard.empty_prescription') }}</p>
        @endif
    </div>

    <!-- Quick Links / Modules -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.quick_access') }}</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.exams.index') }}" class="module-link">
                <span>{{ __('admin.dashboard.manage_exams') }}</span>
                <span>→</span>
            </a>
            <a href="{{ route('admin.laboratories.index') }}" class="module-link">
                <span>{{ __('admin.dashboard.manage_labs') }}</span>
                <span>→</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="module-link">
                <span>{{ __('admin.dashboard.manage_users') }}</span>
                <span>→</span>
            </a>
        </div>
    </div>

</div>

<!-- ================= RECENT PRESCRIPTIONS & LABS ================= -->
<div class="charts-grid anim anim-4">

    <!-- Recent Prescriptions -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.prescriptions_recent') }}</h3>
        @if(count($recentPrescriptions) > 0)
            <div class="recent-list">
                @php
                    $avatarColors = ['#0066ff','#16a34a','#9333ea','#ea580c','#0891b2'];
                @endphp
                @foreach($recentPrescriptions as $i => $rx)
                    @php
                        $doctorName = $rx['doctor_name'];
                        $patientName = $rx['patient_name'];
                        $labName = $rx['lab_name'];
                        $initials = strtoupper(substr($doctorName, 0, 1));
                        $color = $avatarColors[$i % count($avatarColors)];
                        $statusMap = [
                            'pending' => [__('admin.dashboard.status_pending'), 'pill-pending'],
                            'assigned' => [__('admin.dashboard.status_assigned'), 'pill-assigned'],
                            'collected' => [__('admin.dashboard.status_collected'), 'pill-collected'],
                            'processing' => [__('admin.dashboard.status_processing'), 'pill-processing'],
                            'completed' => [__('admin.dashboard.status_completed'), 'pill-completed'],
                            'cancelled' => [__('admin.dashboard.status_cancelled'), 'pill-cancelled'],
                        ];
                        $statusInfo = $statusMap[$rx['status']] ?? [__('admin.dashboard.status_unknown'), 'pill-pending'];
                    @endphp
                    <div class="recent-item">
                        <div class="recent-item-avatar" style="background:{{ $color }};">{{ $initials }}</div>
                        <div class="recent-item-info">
                            <div class="recent-item-title">{{ $doctorName }} → {{ $patientName }}</div>
                            <div class="recent-item-sub">{{ $labName }} · {{ $rx['created_at'] }}</div>
                        </div>
                        <span class="status-pill {{ $statusInfo[1] }}">
                            <span class="dot"></span>
                            {{ $statusInfo[0] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:13px; color:#64748b; font-style:italic; margin:0;">{{ __('admin.dashboard.empty_recent') }}</p>
        @endif
    </div>

    <!-- Labs Distribution + Quick Stats -->
    <div class="chart-container">
        <h3>{{ __('admin.dashboard.cities_distribution') }}</h3>

        <div class="summary-row" style="margin-bottom:20px;">
            <div class="summary-card">
                <div class="summary-card-value" style="color:#0066ff;">{{ $activeLabs }}</div>
                <div class="summary-card-label">{{ __('admin.dashboard.active_labs') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-value" style="color:#16a34a;">{{ $todayPrescriptions }}</div>
                <div class="summary-card-label">{{ __('admin.dashboard.today') }}</div>
            </div>
        </div>

        @if(count($labsPerCity) > 0)
            @php
                $maxCity = max($labsPerCity);
            @endphp
            <div style="display:flex; flex-direction:column; gap:4px;">
                @foreach($labsPerCity as $city => $count)
                    <div class="city-bar">
                        <span class="city-bar-name">{{ $city }}</span>
                        <div class="city-bar-track">
                            <div class="city-bar-fill" style="width:{{ ($count / $maxCity) * 100 }}%;"></div>
                        </div>
                        <span class="city-bar-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:13px; color:#64748b; font-style:italic; margin:0;">{{ __('admin.dashboard.empty_center') }}</p>
        @endif
    </div>

</div>
@endsection

@section('scripts')
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
                    label: '@lang('admin.dashboard.prescription_volume')',
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
                labels: ['@lang('admin.dashboard.status_pending')', '@lang('admin.dashboard.status_assigned')', '@lang('admin.dashboard.status_collected')', '@lang('admin.dashboard.status_processing')', '@lang('admin.dashboard.status_completed')', '@lang('admin.dashboard.status_cancelled')'],
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
