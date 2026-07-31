@extends('layouts.center')

@section('title', 'Suivi des Échantillons - Medix eSanté')

@section('content')
<div class="space-y-6 select-none">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#1e293b]">Suivi des Échantillons</h1>
            <p class="text-sm text-[#64748b] mt-2">Gérez le suivi des échantillons par code-barres.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('center.samples.scan') }}" class="border border-[#e2e8f0] hover:bg-[#f8fafc] text-[#64748b] font-bold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                Scanner
            </a>
            <a href="{{ route('center.samples.create') }}" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-md transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nouvel Échantillon
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-x-auto shadow-xs">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Code</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Patient</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Examen</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Type</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Statut</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Emplacement</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Date</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($samples as $sample)
                <tr class="hover:bg-[#F8FAFC]/70 transition">
                    <td class="p-4 font-mono font-bold text-[#1e293b]">{{ $sample->sample_code }}</td>
                    <td class="p-4">{{ $sample->patient->user->first_name }} {{ $sample->patient->user->last_name }}</td>
                    <td class="p-4">{{ $sample->examRequestItem->exam->name ?? 'N/A' }}</td>
                    <td class="p-4"><span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full">{{ $sample->material_type ?? '-' }}</span></td>
                    <td class="p-4 text-center">
                        @php $statusClasses = ['pending'=>'bg-slate-100 text-slate-700','collected'=>'bg-blue-100 text-blue-700','in_transit'=>'bg-amber-100 text-amber-700','received'=>'bg-purple-100 text-purple-700','processing'=>'bg-cyan-100 text-cyan-700','completed'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusClasses[$sample->status] ?? 'bg-slate-100' }}">
                            {{ __('samples.status.'.$sample->status) ?? $sample->status }}
                        </span>
                    </td>
                    <td class="p-4 text-center text-[#64748b] text-xs">{{ $sample->storage_location ?? '-' }}</td>
                    <td class="p-4 text-[#64748b] text-xs">{{ $sample->created_at->format('d/m/Y') }}</td>
                    <td class="p-4 text-right">
                        <a href="{{ route('center.samples.show', $sample->id) }}" class="text-[#7C3AED] hover:text-[#5B21B6] font-bold text-xs">Voir</a>
                        <a href="{{ route('center.samples.barcode', $sample->id) }}" target="_blank" class="text-[#64748b] hover:text-[#1e293b] font-bold text-xs ml-2">Code-barres</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-8 text-center text-[#94a3b8]">Aucun échantillon.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $samples->links() }}</div>
</div>
@endsection
