@extends('layouts.center')

@section('title', 'Tableau de bord Centre Médical')

@section('content')

<div class="space-y-8 select-none">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-[#1e293b]">
            Bienvenue,
            <span class="text-[#7C3AED]">
                {{ auth()->user()->staff->laboratory->name }}
            </span>
            !
        </h1>

        <p class="text-sm text-[#64748b] mt-2">
            Gérez votre laboratoire, les demandes d'analyses, les stocks et les équipements médicaux.
        </p>
    </div>


    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">


        <!-- Equipment -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="text-3xl font-black text-slate-800">
                {{ $stats['equipment_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Équipements
            </p>
        </div>



        <!-- Consumables -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black text-slate-800">
                {{ $stats['consumables_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Consommables
            </p>

        </div>



        <!-- Low stock -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black 
                {{ $stats['low_stock_count'] > 0 ? 'text-red-600':'text-slate-800' }}">
                {{ $stats['low_stock_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Stock faible
            </p>

        </div>



        <!-- Maintenance -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black
                {{ $stats['active_maintenance_count'] > 0 ? 'text-orange-600':'text-slate-800' }}">
                {{ $stats['active_maintenance_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Maintenance
            </p>

        </div>



        <!-- Exam requests -->
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 shadow-sm">
            <div class="text-3xl font-black text-[#7C3AED]">
                {{ $workload['total'] }}
            </div>
            <p class="text-xs uppercase font-bold text-purple-700 mt-2">
                Demandes d'analyses
            </p>
        </div>

        <!-- Facturation -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 shadow-sm">
            <div class="text-3xl font-black text-emerald-700">
                {{ $billingPending ?? 0 }}
            </div>
            <p class="text-xs uppercase font-bold text-emerald-700 mt-2">
                Factures en attente
            </p>
            @if(($billingCount ?? 0) > 0)
            <p class="text-[10px] text-emerald-600 mt-1">{{ $billingCount }} facture(s) · {{ number_format($billingRevenue ?? 0, 2) }} DT encaissés</p>
            @endif
        </div>

        <!-- Échantillons -->
        <div class="bg-cyan-50 border border-cyan-200 rounded-2xl p-5 shadow-sm">
            <div class="text-3xl font-black text-cyan-700">
                {{ $sampleActive ?? 0 }}
            </div>
            <p class="text-xs uppercase font-bold text-cyan-700 mt-2">
                Échantillons actifs
            </p>
            <p class="text-[10px] text-cyan-600 mt-1">{{ $sampleCount ?? 0 }} totale(s)</p>
        </div>

    </div>



    <!-- Main sections -->

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">



        <!-- Laboratory information -->

        <div class="bg-white border border-slate-200 rounded-2xl p-6">

            <h3 class="font-bold text-slate-800 mb-5">
                Informations du laboratoire
            </h3>


            <div class="space-y-3 text-sm">


                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Nom
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->staff->laboratory->name }}
                    </span>

                </div>



                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Responsable
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->first_name }}
                        {{ auth()->user()->last_name }}
                    </span>

                </div>



                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Email
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->email }}
                    </span>

                </div>



                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Ville
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->staff->laboratory->city ?? 'Non renseignée' }}
                    </span>

                </div>


            </div>

        </div>




        <!-- Quick actions -->

        <div class="bg-white border border-slate-200 rounded-2xl p-6">


            <h3 class="font-bold text-slate-800 mb-5">
                Actions rapides
            </h3>



            <div class="grid grid-cols-2 gap-4">


                <a href="{{ route('center.working-hours') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Horaires
                </a>



                <a href="{{ route('center.consumables') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Stock
                </a>



                <a href="{{ route('center.equipment') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Équipements
                </a>



                <a href="{{ route('center.exam-requests') }}"
                   class="p-4 rounded-xl bg-purple-50 text-purple-700 font-semibold text-center transition">
                    Analyses
                </a>


            </div>


        </div>


    </div>




    <!-- Workload View (Task 3.9) -->
    <div class="space-y-6">

        <!-- Section header -->
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#1e293b] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm6.75-9.75c0-.621.504-1.125 1.125-1.125h2.25C13.496 2.25 14 2.754 14 3.375v16.5c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V3.375zm6.75 5.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v10.875c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V9z"/>
                </svg>
                Charge de travail actuelle
            </h2>
            <a href="{{ route('center.exam-requests') }}" class="text-xs font-bold text-[#7C3AED] hover:underline uppercase tracking-wider">
                Voir les demandes →
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Per-status breakdown -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-5">
                    Répartition par statut
                    <span class="ml-2 text-xs text-slate-400 normal-case font-medium">(total : {{ $workload['total'] }})</span>
                </h3>

                @php
                    $statusRows = [
                        ['key' => 'pending',    'label' => 'En attente',      'color' => 'bg-amber-400',   'text' => 'text-amber-700'],
                        ['key' => 'assigned',   'label' => 'Labo sélectionné','color' => 'bg-teal-500',    'text' => 'text-teal-700'],
                        ['key' => 'collected',  'label' => 'Collectée',        'color' => 'bg-blue-400',    'text' => 'text-blue-700'],
                        ['key' => 'processing', 'label' => 'En traitement',   'color' => 'bg-purple-500',  'text' => 'text-purple-700'],
                        ['key' => 'completed',  'label' => 'Complétée',        'color' => 'bg-green-400',   'text' => 'text-green-700'],
                        ['key' => 'cancelled',  'label' => 'Annulée',          'color' => 'bg-red-400',     'text' => 'text-red-700'],
                    ];
                    $total = max($workload['total'], 1);
                @endphp

                <div class="space-y-3">
                    @foreach ($statusRows as $row)
                        @php $count = $workload[$row['key']]; $pct = round($count / $total * 100); @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-semibold text-slate-600">{{ $row['label'] }}</span>
                                <span class="text-xs font-black {{ $row['text'] }}">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="{{ $row['color'] }} h-2 rounded-full transition-all duration-700"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 7-day Volume Chart -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider">
                        Volume — 7 derniers jours
                    </h3>
                    @php
                        $values = array_values($last7Days);
                        $total7 = array_sum($values);
                        $prevValues = array_values($last7PrevDays ?? []);
                        $prevTotal = array_sum($prevValues);
                        $trend = $prevTotal > 0 ? round((($total7 - $prevTotal) / $prevTotal) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-1.5 text-xs font-bold {{ $trend >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        @if($trend > 0)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                            +{{ $trend }}%
                        @elseif($trend < 0)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0 0V8.25m0 11.25H8.25"/></svg>
                            {{ $trend }}%
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                            0%
                        @endif
                        <span class="text-[10px] text-slate-400 font-semibold">vs semaine préc.</span>
                    </div>
                </div>

                @php
                    $days   = array_keys($last7Days);
                    $values = array_values($last7Days);
                    $labels = collect($days)->map(fn($d) => \Carbon\Carbon::parse($d)->format('D d'))->toArray();
                    $maxVal = max(max($values), 1);
                @endphp

                <div class="relative" style="height: 220px;">
                    <canvas id="volumeChart"></canvas>
                </div>

                <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#7C3AED]"></span>
                            <span class="text-[10px] font-semibold text-slate-500">Demandes</span>
                        </div>
                        @php
                            $avg = $total7 / 7;
                            $peak = max($values);
                            $peakDay = $days[array_search($peak, $values)];
                        @endphp
                        <div class="text-[10px] text-slate-400">
                            Moy. <strong class="text-slate-600">{{ number_format($avg, 1) }}</strong>/jour
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Total 7j : <strong class="text-slate-600">{{ $total7 }}</strong> demande(s)
                        @if($peak > 0)
                            · Pic : <strong class="text-[#7C3AED]">{{ $peak }}</strong> {{ \Carbon\Carbon::parse($peakDay)->format('D') }}
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Revenue & Top Exams -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Revenue -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Revenus (Facturation)
            </h3>
            <div class="text-4xl font-black text-green-600 mb-2">
                {{ number_format($billingRevenue ?? 0, 2) }} DT
            </div>
            <p class="text-xs text-slate-500">Total encaissé — factures payées</p>
            <div class="grid grid-cols-3 gap-3 pt-4 mt-4 border-t border-slate-100">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Facturé</p>
                    <p class="text-sm font-bold text-slate-700">{{ number_format($billingBilled ?? 0, 2) }} DT</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase">En attente</p>
                    <p class="text-sm font-bold text-amber-600">{{ number_format($billingOutstanding ?? 0, 2) }} DT</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Payées</p>
                    <p class="text-sm font-bold text-emerald-600">{{ $billingPaid ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Top Exams -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/>
                </svg>
                Top 5 Examens Demandés
            </h3>
            @if(count($topExams) > 0)
                <div class="space-y-3">
                    @foreach($topExams as $i => $exam)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                            <span class="w-7 h-7 rounded-lg bg-[#7C3AED]/10 flex items-center justify-center text-[#7C3AED] font-bold text-xs">{{ $i + 1 }}</span>
                            <span class="flex-1 text-sm font-semibold text-slate-700">{{ $exam['name'] }}</span>
                            <span class="text-xs font-bold text-[#7C3AED] bg-[#7C3AED]/10 px-2 py-1 rounded">{{ $exam['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 italic">Aucune donnée disponible.</p>
            @endif
        </div>

    </div>

</div>


@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const ctx = document.getElementById('volumeChart');
    if (!ctx) return;

    const labels = {!! json_encode($labels) !!};
    const data   = {!! json_encode($values) !!};

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(124, 58, 237, 0.25)');
    gradient.addColorStop(0.5, 'rgba(124, 58, 237, 0.08)');
    gradient.addColorStop(1, 'rgba(124, 58, 237, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Demandes',
                data: data,
                borderColor: '#7C3AED',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointBackgroundColor: '#7C3AED',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#7C3AED',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 11, weight: '600', family: 'Inter, sans-serif' },
                    bodyFont: { size: 13, weight: '800', family: 'Inter, sans-serif' },
                    padding: { top: 10, bottom: 10, left: 14, right: 14 },
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        title: function(items) {
                            return items[0].label;
                        },
                        label: function(item) {
                            return item.formattedValue + ' demande(s)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 10, weight: '600', family: 'Inter, sans-serif' },
                        color: '#94a3b8',
                        padding: 6,
                    }
                },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#f1f5f9', lineWidth: 1 },
                    ticks: {
                        font: { size: 10, weight: '600', family: 'Inter, sans-serif' },
                        color: '#94a3b8',
                        padding: 8,
                        stepSize: 1,
                        callback: function(value) {
                            if (Number.isInteger(value)) return value;
                            return '';
                        }
                    }
                }
            }
        }
    });
})();
</script>
@endsection