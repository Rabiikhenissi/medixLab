@extends('layouts.center')

@section('title', 'Modifier les résultats - Medix eSanté')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 select-none">
    {{-- Header block --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1e293b]">
                Modifier les résultats
            </h1>
            <p class="text-sm text-[#64748b] mt-1.5 font-medium">
                Saisissez les nouvelles valeurs des paramètres pour l'examen de <strong class="text-[#7C3AED]">{{ $result->examRequestItem->examRequest->patient->user->first_name }} {{ $result->examRequestItem->examRequest->patient->user->last_name }}</strong>.
            </p>
        </div>
        <a href="{{ route('center.exam-requests') }}" class="inline-flex items-center gap-1.5 bg-white border border-[#e2e8f0] hover:bg-[#F8FAFC] text-[#64748b] font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    {{-- Alert Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 text-sm font-semibold">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Form Card --}}
    <form method="POST" action="{{ route('center.results.update', $result) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Info Banner --}}
        <div class="bg-gradient-to-br from-[#7C3AED]/5 to-[#7C3AED]/10 border border-[#7C3AED]/15 rounded-2xl p-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-[#7C3AED]/20 shadow-sm text-[#7C3AED]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-[#1e293b] text-base">{{ $result->examRequestItem->exam->name }}</h3>
                    <p class="text-xs text-[#64748b] mt-0.5">Examen prescrit par le Dr. {{ $result->examRequestItem->examRequest->doctor->user->first_name }} {{ $result->examRequestItem->examRequest->doctor->user->last_name }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-50 text-[#7C3AED] border border-[#7C3AED]/20">
                Code: {{ $result->examRequestItem->exam->code ?? 'N/A' }}
            </span>
        </div>

        {{-- Parameters section --}}
        <div class="space-y-4">
            <h4 class="text-xs font-bold text-[#64748b] uppercase tracking-wider select-none mb-1">
                Paramètres de l'analyse
            </h4>

            @foreach($result->details as $index => $detail)
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs hover:border-[#7C3AED]/30 transition flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-bold text-[#1e293b] block truncate">
                            {{ $detail->parameter }}
                        </span>
                        @if($detail->reference_range)
                            <span class="inline-flex items-center text-[10px] text-[#64748b] mt-1 bg-[#F8FAFC] border border-[#e2e8f0] px-2 py-0.5 rounded-md font-medium">
                                Référence: {{ $detail->reference_range }}
                            </span>
                        @endif
                        <input type="hidden" name="parameters[{{ $index }}][name]" value="{{ $detail->parameter }}">
                        <input type="hidden" name="parameters[{{ $index }}][range]" value="{{ $detail->reference_range }}">
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative w-full md:w-44">
                            <input
                                type="text"
                                class="w-full pl-3 pr-3 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm font-semibold bg-[#F8FAFC]/50 hover:bg-[#F8FAFC]"
                                placeholder="Valeur"
                                name="parameters[{{ $index }}][value]"
                                value="{{ old("parameters.{$index}.value", $detail->value) }}"
                                required
                            >
                        </div>

                        <div class="relative w-full md:w-36">
                            <select
                                name="parameters[{{ $index }}][status]"
                                class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm font-semibold bg-[#F8FAFC]/50 hover:bg-[#F8FAFC] appearance-none"
                            >
                                <option value="normal" {{ old("parameters.{$index}.status", $detail->status) === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old("parameters.{$index}.status", $detail->status) === 'high' ? 'selected' : '' }}>Elevé</option>
                                <option value="low" {{ old("parameters.{$index}.status", $detail->status) === 'low' ? 'selected' : '' }}>Bas</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Interpretation Textarea --}}
        <div class="space-y-2">
            <label for="interpretation" class="text-xs font-bold text-[#64748b] uppercase tracking-wider block">
                Interprétation Globale du Laboratoire
            </label>
            <textarea
                id="interpretation"
                name="interpretation"
                rows="4"
                class="w-full p-4 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm leading-relaxed"
                placeholder="Rédigez l'analyse ou l'interprétation des résultats..."
            >{{ old('interpretation', $result->interpretation) }}</textarea>
        </div>

        {{-- Submit button --}}
        <div class="flex justify-end pt-4 border-t border-[#e2e8f0]">
            <button
                type="submit"
                class="w-full md:w-auto bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-8 py-3.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-purple-200 text-sm uppercase tracking-wider text-center cursor-pointer"
            >
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
