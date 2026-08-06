<x-layouts.patient>
    <x-slot:title>{{ __('patient.medical_history.title') }} — Medix eSanté</x-slot:title>

    @section('content')
    <div class="w-full max-w-[1100px] mx-auto">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0D9488] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#1e293b]">{{ __('patient.medical_history.title') }}</h1>
                        <p class="text-sm text-[#64748b] mt-0.5">
                            {{ $user->first_name }} {{ $user->last_name }} —
                            <span class="font-semibold text-[#0D9488]">{{ $patient->patient_code }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('patient.dashboard') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-[#64748b] hover:text-[#0D9488] transition uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('patient.dashboard.back_label') }}
                </a>
            </div>

            {{-- Summary chips --}}
            @php
                $total     = $examRequests->count();
                $completed = $examRequests->where('status', 'completed')->count();
                $active    = $examRequests->whereNotIn('status', ['completed', 'cancelled'])->count();
                $cancelled = $examRequests->where('status', 'cancelled')->count();
            @endphp
            <div class="flex flex-wrap gap-3 mb-8">
                <div class="flex items-center gap-2 px-4 py-2 bg-[#0D9488]/10 border border-[#0D9488]/20 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-[#0D9488]"></span>
                    <span class="text-xs font-bold text-[#0D9488]">{{ __('patient.medical_history.total_requests', ['n' => $total]) }}</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-xs font-bold text-green-700">{{ __('patient.medical_history.completed_count', ['n' => $completed]) }}</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="text-xs font-bold text-amber-700">{{ __('patient.medical_history.in_progress', ['n' => $active]) }}</span>
                </div>
                @if ($cancelled > 0)
                <div class="flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    <span class="text-xs font-bold text-red-700">{{ __('patient.medical_history.cancelled_count', ['n' => $cancelled]) }}</span>
                </div>
                @endif
            </div>

            {{-- Empty state --}}
            @if ($examRequests->isEmpty())
                <div class="text-center py-16 text-[#94a3b8]">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg font-semibold">{{ __('patient.medical_history.empty_title') }}</p>
                    <p class="text-sm mt-1 opacity-70">{{ __('patient.medical_history.empty_hint') }}</p>
                </div>
            @else
                {{-- Timeline --}}
                <div class="relative">
                    {{-- vertical line --}}
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#0D9488] via-[#e2e8f0] to-transparent"></div>

                    <div class="space-y-8">
                        @foreach ($examRequests as $req)
                            @php
                                $statusColors = [
                                    'pending'    => ['dot' => 'bg-amber-400',  'badge' => 'text-amber-700 bg-amber-50 border-amber-200'],
                                    'assigned'   => ['dot' => 'bg-teal-500',   'badge' => 'text-teal-700 bg-teal-50 border-teal-200'],
                                    'collected'  => ['dot' => 'bg-blue-400',   'badge' => 'text-blue-700 bg-blue-50 border-blue-200'],
                                    'processing' => ['dot' => 'bg-purple-500', 'badge' => 'text-purple-700 bg-purple-50 border-purple-200'],
                                    'completed'  => ['dot' => 'bg-green-500',  'badge' => 'text-green-700 bg-green-50 border-green-200'],
                                    'cancelled'  => ['dot' => 'bg-red-400',    'badge' => 'text-red-700 bg-red-50 border-red-200'],
                                ];
                                $statusLabels = [
                                    'pending'    => __('patient.status.pending'),
                                    'assigned'   => __('patient.status.assigned'),
                                    'collected'  => __('patient.status.collected'),
                                    'processing' => __('patient.status.processing'),
                                    'completed'  => __('patient.status.completed'),
                                    'cancelled'  => __('patient.status.cancelled'),
                                ];
                                $sc    = $statusColors[$req->status] ?? ['dot' => 'bg-slate-400', 'badge' => 'text-slate-700 bg-slate-50 border-slate-200'];
                                $label = $statusLabels[$req->status] ?? $req->status;
                            @endphp

                            <div class="flex gap-6 group">
                                {{-- Timeline dot --}}
                                <div class="relative flex-shrink-0 flex flex-col items-center" style="width:40px">
                                    <div class="w-10 h-10 rounded-full {{ $sc['dot'] }} flex items-center justify-center shadow-md border-2 border-white z-10">
                                        @if ($req->status === 'completed')
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif ($req->status === 'cancelled')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card --}}
                                <div class="flex-1 bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow duration-200 mb-1">

                                    {{-- Card header --}}
                                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                                        <div>
                                            <p class="text-[10px] text-[#94a3b8] font-semibold uppercase tracking-wider mb-0.5">
                                                {{ $req->created_at->format('d M Y — H:i') }}
                                                <span class="ml-2 text-[#cbd5e1]">{{ $req->created_at->diffForHumans() }}</span>
                                            </p>
                                            <h3 class="font-bold text-[#1e293b]">
                                                Dr. {{ $req->doctor->user->first_name }} {{ $req->doctor->user->last_name }}
                                            </h3>
                                            <p class="text-sm text-[#64748b]">{{ $req->doctor->speciality }}</p>
                                        </div>
                                        <div class="flex flex-wrap gap-2 items-center">
                                            <span class="px-3 py-1 text-[10px] font-extrabold border rounded-full uppercase tracking-wider {{ $sc['badge'] }}">
                                                {{ $label }}
                                            </span>
                                            @if ($req->laboratory)
                                                <span class="px-3 py-1 text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 rounded-full">
                                                    🏥 {{ $req->laboratory->name }}
                                                </span>
                                            @endif
                                            @if ($req->status === 'completed' && $req->approved_by_doctor)
                                                <a href="{{ route('patient.print-exam-request', $req->id) }}?pdf=1"
                                                   target="_blank"
                                                   class="flex items-center gap-1 px-3 py-1 text-[10px] font-bold text-[#7C3AED] bg-[#7C3AED]/10 border border-[#7C3AED]/20 rounded-full hover:bg-[#7C3AED]/20 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                    {{ __('patient.medical_history.download') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Clinical notes --}}
                                    @if ($req->clinical_notes)
                                        <div class="mb-4 p-3 bg-[#F8FAFC] border border-[#e2e8f0]/60 rounded-xl text-sm text-[#64748b] italic">
                                            "{{ $req->clinical_notes }}"
                                        </div>
                                    @endif

                                    {{-- Exams list --}}
                                    <div class="space-y-2">
                                        <p class="text-[10px] font-bold text-[#1e293b] uppercase tracking-wider mb-2">
                                            {{ __('patient.medical_history.prescribed_exams', ['n' => $req->items->count()]) }}
                                        </p>
                                        @foreach ($req->items as $item)
                                            <div class="flex items-start gap-3 p-3 bg-[#F8FAFC]/60 border border-[#e2e8f0]/60 rounded-xl">
                                                <div class="w-7 h-7 rounded-lg bg-[#0D9488]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3.5 h-3.5 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-sm text-[#1e293b]">{{ $item->exam->name }}</p>
                                                    @if ($item->exam->category)
                                                        <p class="text-[10px] text-[#0D9488] font-medium mt-0.5">{{ $item->exam->category }}</p>
                                                    @endif

                                                    {{-- Show result if available & approved --}}
                                                    @if ($req->status === 'completed' && $req->approved_by_doctor && $item->resultLabo)
                                                        <div class="mt-2 space-y-1">
                                                            @foreach ($item->resultLabo->details as $detail)
                                                                <div class="flex items-center justify-between text-xs">
                                                                    <span class="text-[#475569] font-medium">{{ $detail->parameter }}@if($detail->unit) <span class="text-[9px] font-semibold text-[#7C3AED] bg-[#7C3AED]/10 px-1 py-0.5 rounded border border-[#7C3AED]/20">{{ $detail->unit }}</span>@endif</span>
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="font-bold {{ $detail->status === 'normal' ? 'text-green-600' : ($detail->status === 'high' ? 'text-red-600' : ($detail->status === 'critical' ? 'text-purple-700' : 'text-amber-600')) }}">
                                                                            {{ $detail->value }}
                                                                        </span>
                                                                        @if ($detail->reference_range)
                                                                            <span class="text-[#94a3b8]">({{ $detail->reference_range }})</span>
                                                                        @endif
                                                                        @php $statusIcon = ['normal' => '✓', 'high' => '↑', 'low' => '↓', 'critical' => '⚠'][$detail->status] ?? ''; @endphp
                                                                        <span class="text-[10px] font-black {{ $detail->status === 'normal' ? 'text-green-500' : ($detail->status === 'critical' ? 'text-purple-600' : 'text-red-500') }}">{{ $statusIcon }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if ($item->resultLabo->interpretation)
                                                                <p class="text-xs text-[#64748b] mt-1 italic border-t border-[#e2e8f0]/60 pt-1">
                                                                    {{ $item->resultLabo->interpretation }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Doctor interpretation --}}
                                    @if ($req->status === 'completed' && $req->approved_by_doctor && $req->doctor_interpretation)
                                        <div class="mt-4 p-4 bg-purple-50/50 border border-purple-200/60 rounded-xl">
                                            <p class="text-[10px] font-bold text-purple-700 uppercase tracking-wider mb-1">{{ __('patient.medical_history.doctor_interpretation') }}</p>
                                            <p class="text-sm text-[#475569]">{{ $req->doctor_interpretation }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    @endsection
</x-layouts.patient>
