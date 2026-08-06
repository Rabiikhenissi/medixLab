@extends('layouts.center')

@section('title', __('center.cnam.title').' - Medix eSanté')

@section('content')
<div class="space-y-6 select-none">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#1e293b]">{{ __('center.cnam.title') }}</h1>
            <p class="text-sm text-[#64748b] mt-2">{{ __('center.cnam.subtitle') }}</p>
        </div>
        <button onclick="document.getElementById('addCnamModal').classList.remove('hidden')" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-md transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('center.cnam.new_code') }}
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-x-auto shadow-xs">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.cnam.code_cnam') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.cnam.exam') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('center.cnam.valeur_b') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('center.cnam.rate') }}</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('common.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($nomenclatures as $n)
                <tr class="hover:bg-[#F8FAFC]/70 transition">
                    <td class="p-4 font-mono font-bold text-[#7C3AED]">{{ $n->code_cnam }}</td>
                    <td class="p-4">{{ $n->exam_name }}</td>
                    <td class="p-4 text-right font-bold">{{ number_format($n->valeur_b, 3) }} TND</td>
                    <td class="p-4 text-right">{{ $n->taux }}%</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $n->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $n->is_active ? __('center.status.active') : __('center.status.inactive') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $nomenclatures->links() }}</div>
</div>

<div id="addCnamModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-2xl">
        <h3 class="text-lg font-bold text-[#1e293b] mb-4">{{ __('center.cnam.add_title') }}</h3>
        <form method="POST" action="{{ route('center.cnam.store') }}" class="space-y-3">
            @csrf
            <div><label class="text-[10px] font-bold text-[#64748b] uppercase">{{ __('center.cnam.code_cnam') }}</label>
            <input type="text" name="code_cnam" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></div>
            <div><label class="text-[10px] font-bold text-[#64748b] uppercase">{{ __('center.cnam.exam_name') }}</label>
            <input type="text" name="exam_name" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></div>
            <div><label class="text-[10px] font-bold text-[#64748b] uppercase">{{ __('center.cnam.valeur_b_tnd') }}</label>
            <input type="number" step="0.001" name="valeur_b" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></div>
            <div><label class="text-[10px] font-bold text-[#64748b] uppercase">{{ __('center.cnam.rate_cnam') }}</label>
            <input type="number" step="0.01" name="taux" value="0" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></div>
            <div><label class="text-[10px] font-bold text-[#64748b] uppercase">{{ __('center.cnam.description') }}</label>
            <textarea name="description" rows="2" class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] outline-none"></textarea></div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2.5 rounded-xl text-sm transition">{{ __('common.add') }}</button>
                <button type="button" onclick="document.getElementById('addCnamModal').classList.add('hidden')" class="text-[#64748b] font-medium px-4 py-2.5 text-sm">{{ __('common.cancel') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
