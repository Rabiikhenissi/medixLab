@extends('layouts.center')

@section('title', 'Examens Disponibles - Medix eSanté')

@section('content')
<div class="max-w-6xl mx-auto select-none">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1e293b]">Examens Disponibles</h1>
            <p class="text-sm text-[#64748b] mt-1">Gérez les examens proposés par votre laboratoire et leurs prix</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#7C3AED]/10 text-[#7C3AED] border border-[#7C3AED]/20">
            {{ $availableExams->count() }} examen(s) configuré(s)
        </span>
    </div>

    {{-- Add New Exam Form --}}
    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 mb-6 shadow-xs">
        <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un examen
        </h3>
        <form action="{{ route('center.available-exams.store') }}" method="POST" class="flex flex-col sm:flex-row items-end gap-3">
            @csrf
            <div class="flex-1 w-full">
                <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest block mb-1.5">Examen</label>
                <select name="exam_id" required class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                    <option value="">— Sélectionner un examen —</option>
                    @foreach($allExams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }} @if($exam->code)({{ $exam->code }})@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-widest block mb-1.5">Prix (TND)</label>
                <input type="number" name="price" step="0.01" min="0" required placeholder="0.00"
                    class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] bg-white text-[#1e293b]">
            </div>
            <button type="submit" class="w-full sm:w-auto bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-6 py-2.5 rounded-xl transition text-xs uppercase tracking-wider shadow-sm cursor-pointer whitespace-nowrap">
                Ajouter
            </button>
        </form>
    </div>

    {{-- Available Exams List --}}
    @if($availableExams->count() > 0)
        <div class="space-y-3">
            @foreach($availableExams as $ae)
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center gap-4 shadow-xs hover:border-[#7C3AED]/25 transition">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-sm text-[#1e293b]">{{ $ae->exam->name }}</h4>
                            @if($ae->exam->code)
                                <span class="text-[9px] font-bold text-[#7C3AED] bg-[#7C3AED]/10 px-1.5 py-0.5 rounded border border-[#7C3AED]/20">{{ $ae->exam->code }}</span>
                            @endif
                            @if($ae->exam->category)
                                <span class="text-[9px] font-bold text-[#64748b] bg-[#f1f5f9] px-1.5 py-0.5 rounded border border-[#e2e8f0]">{{ $ae->exam->category }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        {{-- Price --}}
                        <form action="{{ route('center.available-exams.update', $ae) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="number" name="price" step="0.01" min="0" value="{{ $ae->price }}"
                                class="w-24 px-2 py-1.5 border border-[#e2e8f0] rounded-lg text-xs font-bold text-[#1e293b] focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] bg-[#F8FAFC] text-center">
                            <input type="hidden" name="is_active" value="{{ $ae->is_active ? '1' : '0' }}">
                            <button type="submit" class="text-[10px] font-bold text-white bg-[#7C3AED] hover:bg-[#6D28D9] px-2.5 py-1.5 rounded-lg transition cursor-pointer">Prix</button>
                        </form>

                        {{-- Active Toggle --}}
                        <form action="{{ route('center.available-exams.toggle', $ae) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border transition cursor-pointer {{ $ae->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                                {{ $ae->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form action="{{ route('center.available-exams.destroy', $ae) }}" method="POST" onsubmit="return swalConfirmSubmit(this, 'Retirer cet examen ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 border border-dashed border-[#cbd5e1] rounded-2xl">
            <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <p class="text-sm font-semibold text-[#94a3b8]">Aucun examen configuré</p>
            <p class="text-xs text-[#94a3b8] mt-1">Ajoutez des examens via le formulaire ci-dessus</p>
        </div>
    @endif
</div>
@endsection
