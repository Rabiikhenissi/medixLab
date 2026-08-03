<x-layouts.doctor>
<x-slot:title>Dossier médical — {{ $patientUser->first_name }} {{ $patientUser->last_name }}</x-slot:title>

@section('content')
<div class="w-full max-w-[1100px] mx-auto py-8 px-4">

    <a href="{{ route('doctor.my-patients') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#0066ff] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à mes patients
    </a>

    {{-- Patient Info Header --}}
    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 md:p-8 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row items-start gap-4">
            <div class="w-14 h-14 rounded-full bg-[#0066ff]/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-[#0066ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-[#1e293b]">{{ $patientUser->first_name }} {{ $patientUser->last_name }}</h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-[#64748b]">
                    <span>Code : <strong>{{ $patient->patient_code }}</strong></span>
                    @if($patient->gender)<span>· Sexe : <strong>{{ $patient->gender === 'M' ? 'Masculin' : 'Féminin' }}</strong></span>@endif
                    @if($patient->date_of_birth)<span>· Né(e) : <strong>{{ $patient->date_of_birth->format('d/m/Y') }} ({{ $patient->date_of_birth->age }} ans)</strong></span>@endif
                    @if($patient->blood_group)<span>· Groupe : <strong>{{ $patient->blood_group }}</strong></span>@endif
                </div>
            </div>
            <a href="{{ route('doctor.chat', $patient) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#0066ff] text-white rounded-xl text-xs font-bold hover:bg-[#0052cc] transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Contacter
            </a>
        </div>
    </div>

    {{-- Exam History --}}
    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 md:p-8 shadow-sm">
        <h2 class="text-sm font-bold text-[#1e293b] mb-5">Historique des examens</h2>

        @forelse($examRequests as $er)
            <div class="mb-4 last:mb-0 p-4 bg-[#f8fafc] rounded-xl border border-[#e2e8f0]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#64748b]">Demande #{{ $er->id }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                            {{ $er->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $er->status }}
                        </span>
                        @if($er->approved_by_doctor)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Validé</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-[#94a3b8]">{{ $er->created_at->format('d/m/Y H:i') }}</span>
                </div>

                @if($er->laboratory)
                <p class="text-[10px] text-[#94a3b8] mb-3">Labo : <strong class="text-[#64748b]">{{ $er->laboratory->name }}</strong></p>
                @endif

                <div class="space-y-2">
                    @foreach($er->items as $item)
                        <div class="pl-3 border-l-2 {{ $item->resultLabo ? 'border-emerald-400' : 'border-[#e2e8f0]' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-[#1e293b]">{{ $item->exam->name }}</span>
                                @if($item->resultLabo)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Résultats disponibles</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-400 border border-slate-200">En attente</span>
                                @endif
                            </div>
                            @if($item->resultLabo && $item->resultLabo->details->count() > 0)
                                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                    @foreach($item->resultLabo->details as $detail)
                                        <div class="p-1.5 bg-white rounded-lg border border-[#e2e8f0] {{ in_array($detail->status, ['high', 'low', 'abnormal', 'critical']) ? ($detail->status === 'critical' ? 'border-l-2 border-l-purple-500' : 'border-l-2 border-l-red-400') : '' }}">
                                            <span class="text-[9px] text-[#94a3b8] block">{{ $detail->parameter }}</span>
                                            <span class="text-xs font-bold text-[#1e293b]">{{ $detail->value }}
                                                @if($detail->unit)<span class="text-[9px] text-[#94a3b8] font-normal"> {{ $detail->unit }}</span>@endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                @if($item->resultLabo->interpretation)
                                    <p class="mt-2 text-[10px] text-[#64748b] italic">Interprétation labo : {{ $item->resultLabo->interpretation }}</p>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($er->doctor_interpretation)
                    <div class="mt-3 p-3 bg-blue-50/50 border border-blue-100 rounded-xl">
                        <p class="text-[9px] font-bold text-blue-600 uppercase tracking-wider mb-1">Mon interprétation</p>
                        <p class="text-xs text-[#1e293b]">{{ $er->doctor_interpretation }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10">
                <svg class="w-12 h-12 mx-auto mb-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                </svg>
                <p class="text-sm font-semibold text-[#94a3b8]">Aucun examen</p>
                <p class="text-xs text-[#94a3b8] mt-1">Ce patient n'a pas encore d'examens prescrits.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
</x-layouts.doctor>