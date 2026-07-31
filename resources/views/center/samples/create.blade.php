@extends('layouts.center')

@section('title', 'Nouvel Échantillon - Medix eSanté')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 select-none">
    <div>
        <h1 class="text-3xl font-bold text-[#1e293b]">Créer un Échantillon</h1>
        <p class="text-sm text-[#64748b] mt-2">Générez un code-barres pour un nouvel échantillon.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-semibold">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('center.samples.store') }}" class="bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-4">
        @csrf
        <div>
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Examen (Demande collectée)</label>
            <select name="exam_request_item_id" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none">
                <option value="">Sélectionner...</option>
                @foreach($collectedItems as $item)
                    <option value="{{ $item->id }}">
                        #{{ $item->examRequest->id }} - {{ $item->examRequest->patient->user->first_name }} {{ $item->examRequest->patient->user->last_name }} - {{ $item->exam->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Type d'échantillon</label>
            <input type="text" name="material_type" class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none" placeholder="Sang, Urine, etc.">
        </div>
        <div>
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Emplacement de stockage</label>
            <input type="text" name="storage_location" class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none" placeholder="Ex: Réfrigérateur A3">
        </div>
        <div>
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Date d'expiration</label>
            <input type="date" name="expiry_date" class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none">
        </div>
        <div>
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Notes</label>
            <textarea name="notes" rows="2" class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-6 py-2.5 rounded-xl text-sm shadow-md transition">Créer l'échantillon</button>
            <a href="{{ route('center.samples.index') }}" class="text-[#64748b] font-medium px-4 py-2.5 text-sm">Annuler</a>
        </div>
    </form>
</div>
@endsection
