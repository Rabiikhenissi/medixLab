@extends('layouts.center')

@section('title', __('center.results.edit_title') . ' - Medix eSanté')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 select-none">
    {{-- Header block --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1e293b]">
                {{ __('center.results.edit_title') }}
            </h1>
            <p class="text-sm text-[#64748b] mt-1.5 font-medium">
                {!! __('center.results.edit_subtitle', ['patient' => '<strong class="text-[#7C3AED]">'.$result->examRequestItem->examRequest->patient->user->first_name.' '.$result->examRequestItem->examRequest->patient->user->last_name.'</strong>']) !!}
            </p>
        </div>
        <a href="{{ route('center.exam-requests') }}" class="inline-flex items-center gap-1.5 bg-white border border-[#e2e8f0] hover:bg-[#F8FAFC] text-[#64748b] font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('common.back') }}
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
                    <p class="text-xs text-[#64748b] mt-0.5">{{ __('center.results.prescribed_by', ['doctor' => $result->examRequestItem->examRequest->doctor->user->first_name.' '.$result->examRequestItem->examRequest->doctor->user->last_name]) }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-50 text-[#7C3AED] border border-[#7C3AED]/20">
                {{ __('center.results.code', ['code' => $result->examRequestItem->exam->code ?? 'N/A']) }}
            </span>
        </div>

        {{-- Parameters section --}}
        <div class="space-y-4">
            <h4 class="text-xs font-bold text-[#64748b] uppercase tracking-wider select-none mb-1">
                {{ __('center.results.parameters') }}
            </h4>

            @foreach($result->details as $index => $detail)
                @php
                    $matchingParam = $result->examRequestItem->exam->parameters->firstWhere('name', $detail->parameter);
                @endphp
                <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs hover:border-[#7C3AED]/30 transition flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-bold text-[#1e293b] block truncate">
                            {{ $detail->parameter }}
                            @if($detail->unit || ($matchingParam && $matchingParam->unit))
                                <span class="text-[10px] font-semibold text-[#7C3AED] bg-[#7C3AED]/10 px-1.5 py-0.5 rounded border border-[#7C3AED]/20 ml-1.5">{{ $detail->unit ?? $matchingParam->unit }}</span>
                            @endif
                        </span>
                        @if($detail->reference_range)
                            <span class="inline-flex items-center text-[10px] text-[#64748b] mt-1 bg-[#F8FAFC] border border-[#e2e8f0] px-2 py-0.5 rounded-md font-medium">
                                {{ __('center.results.reference', ['range' => $detail->reference_range]) }}
                            </span>
                        @endif
                        @if($matchingParam && ($matchingParam->critical_low !== null || $matchingParam->critical_high !== null))
                            <span class="inline-flex items-center text-[10px] text-red-600 mt-1 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md font-bold">
                                {{ __('center.results.critical', ['low' => $matchingParam->critical_low ?? '-', 'high' => $matchingParam->critical_high ?? '-']) }}
                            </span>
                        @endif
                        <input type="hidden" name="parameters[{{ $index }}][name]" value="{{ $detail->parameter }}">
                        <input type="hidden" name="parameters[{{ $index }}][range]" value="{{ $detail->reference_range }}">
                        <input type="hidden" name="parameters[{{ $index }}][unit]" value="{{ $detail->unit ?? ($matchingParam->unit ?? '') }}">
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative w-full md:w-44">
                            <input
                                type="text"
                                class="param-value-input w-full pl-3 pr-3 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm font-semibold bg-[#F8FAFC]/50 hover:bg-[#F8FAFC]"
                                placeholder="{{ __('center.results.value_placeholder') }}"
                                name="parameters[{{ $index }}][value]"
                                data-index="{{ $index }}"
                                data-range="{{ $detail->reference_range ?? '' }}"
                                data-critical-low="{{ $matchingParam->critical_low ?? '' }}"
                                data-critical-high="{{ $matchingParam->critical_high ?? '' }}"
                                value="{{ old("parameters.{$index}.value", $detail->value) }}"
                                required
                            >
                        </div>

                        <div class="relative w-full md:w-36">
                            <select
                                name="parameters[{{ $index }}][status]"
                                data-index="{{ $index }}"
                                class="param-status-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm font-semibold bg-[#F8FAFC]/50 hover:bg-[#F8FAFC] appearance-none"
                            >
                                <option value="normal" {{ old("parameters.{$index}.status", $detail->status) === 'normal' ? 'selected' : '' }}>{{ __('center.results.normal') }}</option>
                                <option value="high" {{ old("parameters.{$index}.status", $detail->status) === 'high' ? 'selected' : '' }}>{{ __('center.results.high') }}</option>
                                <option value="low" {{ old("parameters.{$index}.status", $detail->status) === 'low' ? 'selected' : '' }}>{{ __('center.results.low') }}</option>
                                <option value="critical" {{ old("parameters.{$index}.status", $detail->status) === 'critical' ? 'selected' : '' }}>{{ __('center.results.critical_badge') }}</option>
                            </select>
                        </div>
                        <span data-index="{{ $index }}" class="param-status-badge hidden text-[9px] font-bold px-1.5 py-0.5 rounded uppercase"></span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Consumables & Equipment section --}}
        {{-- Consumables Card (full-width) --}}
        <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-xs font-bold text-[#64748b] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    {{ __('center.results.consumables_used') }}
                </h4>
                <span id="consumable-count-badge" class="hidden inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-[#7C3AED]/10 text-[#7C3AED] border border-[#7C3AED]/20">{{ __('center.results.selected_count', ['count' => 0]) }}</span>
            </div>

            <div id="consumables-list" class="space-y-3 mb-5"></div>

            <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-[#e2e8f0]/60">
                <select id="consumable-select" class="flex-1 px-3 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] text-sm bg-[#F8FAFC]/50 text-[#1e293b]">
                    <option value="">{{ __('center.results.select_consumable') }}</option>
                    @foreach($consumables as $consumable)
                        <option value="{{ $consumable->id }}" data-unit="{{ $consumable->unit }}" data-stock="{{ $consumable->quantity }}" data-name="{{ $consumable->name }}">
                            {{ $consumable->name }} — {{ __('center.results.stock_available', ['stock' => $consumable->quantity, 'unit' => $consumable->unit]) }}
                        </option>
                    @endforeach
                </select>
                <button type="button" id="btn-add-consumable"
                    class="inline-flex items-center gap-2 bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition cursor-pointer select-none shadow-sm shadow-purple-200 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('common.add') }}
                </button>
            </div>
        </div>

        {{-- Equipment Card (full-width grid) --}}
        <div class="grid grid-cols-1 gap-6">

            {{-- Equipment Card --}}
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs">
                <h4 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    {{ __('center.results.equipment_used') }}
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[260px] overflow-y-auto pr-1">
                    @if($equipment->isEmpty())
                        <p class="text-xs text-[#64748b] italic col-span-3">{{ __('center.results.no_machine') }}</p>
                    @else
                        @foreach($equipment as $equip)
                            <label class="flex items-center gap-3 p-3 border border-[#e2e8f0] rounded-xl hover:border-[#7C3AED]/20 cursor-pointer transition select-none">
                                <input type="checkbox" name="equipment[]" value="{{ $equip->id }}" class="rounded text-[#7C3AED] focus:ring-[#7C3AED]/30"
                                    @if($result->equipment->contains($equip->id)) checked @endif
                                >
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-bold text-[#1e293b] block truncate">
                                        {{ $equip->name }}
                                        @if($result->examRequestItem->exam->examEquipment->contains('equipment_id', $equip->id))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase bg-purple-50 text-[#7C3AED] border border-[#7C3AED]/20 ml-1.5">{{ __('center.results.suggested') }}</span>
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-[#64748b] block mt-0.5">{{ __('center.results.serial_type', ['serial' => $equip->serial_number ?? 'N/A', 'type' => $equip->type ?? 'N/A']) }}</span>
                                </div>
                            </label>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Interpretation Textarea --}}
        <div class="space-y-2">
            <label for="interpretation" class="text-xs font-bold text-[#64748b] uppercase tracking-wider block">
                {{ __('center.results.interpretation') }}
            </label>
            <textarea
                id="interpretation"
                name="interpretation"
                rows="4"
                class="w-full p-4 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] transition text-[#1e293b] text-sm leading-relaxed"
                placeholder="{{ __('center.results.interpretation_placeholder') }}"
            >{{ old('interpretation', $result->interpretation) }}</textarea>
        </div>

        {{-- Submit button --}}
        <div class="flex justify-end pt-4 border-t border-[#e2e8f0]">
            <button
                type="submit"
                class="w-full md:w-auto bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-8 py-3.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-purple-200 text-sm uppercase tracking-wider text-center cursor-pointer"
            >
                {{ __('center.results.save_changes') }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const listContainer  = document.getElementById('consumables-list');
    const selectElem     = document.getElementById('consumable-select');
    const btnAdd         = document.getElementById('btn-add-consumable');
    const countBadge     = document.getElementById('consumable-count-badge');

    let selected = [];

    function updateCountBadge() {
        if (selected.length > 0) {
            countBadge.textContent = `${selected.length} ${@json(__('center.results.selected_suffix'))}`;
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }

    function render() {
        listContainer.innerHTML = '';
        if (selected.length === 0) {
            listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 text-[#94a3b8] border-2 border-dashed border-[#e2e8f0] rounded-2xl">
                    <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="text-sm font-semibold">${@json(__('center.results.empty_selected'))}</p>
                    <p class="text-xs mt-1 opacity-70">${@json(__('center.results.empty_hint'))}</p>
                </div>`;
            updateCountBadge();
            return;
        }

        selected.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'group relative flex flex-col sm:flex-row sm:items-center gap-4 p-4 bg-gradient-to-r from-[#F8FAFC] to-white border border-[#e2e8f0] rounded-2xl hover:border-[#7C3AED]/30 hover:shadow-sm transition-all duration-200';
            div.innerHTML = `
                <div class="flex-1 min-w-0">
                    <div class="flex items-center flex-wrap gap-2 mb-1">
                        <span class="text-sm font-bold text-[#1e293b] truncate">${item.name}</span>
                        ${item.isSuggested ? `<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-purple-50 text-[#7C3AED] border border-[#7C3AED]/25">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            ${@json(__('center.results.suggested'))}</span>` : ''}
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            ${@json(__('center.results.stock'))}<strong>${item.stock} ${item.unit}</strong>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="hidden" name="consumables[${index}][id]" value="${item.id}">
                    <label class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider hidden sm:block">${@json(__('center.results.qty'))}</label>
                    <div class="flex items-center border border-[#e2e8f0] rounded-xl overflow-hidden bg-white shadow-xs">
                        <button type="button" class="qty-dec w-8 h-9 flex items-center justify-center text-[#64748b] hover:text-[#7C3AED] hover:bg-[#7C3AED]/5 transition font-bold text-base" data-index="${index}">−</button>
                        <input type="number" name="consumables[${index}][quantity]" value="${item.quantity}" min="1" max="${item.stock}"
                            class="qty-input w-14 h-9 text-center text-sm font-bold text-[#1e293b] border-x border-[#e2e8f0] focus:outline-none focus:bg-[#7C3AED]/5 transition"
                            data-index="${index}">
                        <button type="button" class="qty-inc w-8 h-9 flex items-center justify-center text-[#64748b] hover:text-[#7C3AED] hover:bg-[#7C3AED]/5 transition font-bold text-base" data-index="${index}">+</button>
                    </div>
                    <span class="text-xs font-semibold text-[#64748b]">${item.unit}</span>
                </div>
                <button type="button"
                    class="absolute top-3 right-3 sm:relative sm:top-auto sm:right-auto opacity-0 group-hover:opacity-100 flex items-center gap-1 text-red-400 hover:text-red-600 hover:bg-red-50 px-2 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition border border-transparent hover:border-red-100"
                    onclick="removeConsumable(${index})">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    ${@json(__('center.results.remove'))}
                </button>
            `;
            listContainer.appendChild(div);
        });

        listContainer.querySelectorAll('.qty-dec').forEach(btn => {
            btn.addEventListener('click', () => {
                const i = parseInt(btn.dataset.index);
                if (selected[i].quantity > 1) { selected[i].quantity--; render(); }
            });
        });
        listContainer.querySelectorAll('.qty-inc').forEach(btn => {
            btn.addEventListener('click', () => {
                const i = parseInt(btn.dataset.index);
                if (selected[i].quantity < selected[i].stock) { selected[i].quantity++; render(); }
            });
        });
        listContainer.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', () => {
                const i = parseInt(input.dataset.index);
                let v = parseInt(input.value);
                if (isNaN(v) || v < 1) v = 1;
                if (v > selected[i].stock) v = selected[i].stock;
                selected[i].quantity = v;
                input.value = v;
            });
        });

        updateCountBadge();
    }

    window.removeConsumable = function(index) {
        selected.splice(index, 1);
        render();
    };

    btnAdd.addEventListener('click', function () {
        const opt = selectElem.options[selectElem.selectedIndex];
        const val = selectElem.value;
        if (!val) return;

        if (selected.some(item => item.id === val)) {
            const items = listContainer.querySelectorAll('.group');
            const idx   = selected.findIndex(item => item.id === val);
            if (items[idx]) {
                items[idx].classList.add('ring-2', 'ring-[#7C3AED]/40');
                setTimeout(() => items[idx]?.classList.remove('ring-2', 'ring-[#7C3AED]/40'), 800);
            }
            return;
        }

        const name  = opt.dataset.name || opt.text.split(' —')[0];
        const unit  = opt.dataset.unit;
        const stock = parseInt(opt.dataset.stock);

        if (stock <= 0) {
            selectElem.classList.add('border-red-400');
            setTimeout(() => selectElem.classList.remove('border-red-400'), 1000);
            return;
        }

        selected.push({ id: val, name, unit, stock, quantity: 1, isSuggested: false });
        render();
        selectElem.value = '';
    });

    @if(isset($preloadedConsumables) && count($preloadedConsumables) > 0)
        selected = @json($preloadedConsumables);
    @endif
    render();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function parseRange(rangeStr) {
        if (!rangeStr) return null;
        let cleaned = rangeStr.replace(/\(H\)/gi, '').replace(/\(F\)/gi, '').trim();
        const parts = cleaned.split('/').map(s => s.trim());
        let min = null, max = null;
        for (const part of parts) {
            const m = part.match(/([\d.]+)\s*-\s*([\d.]+)/);
            if (m) {
                const lo = parseFloat(m[1]), hi = parseFloat(m[2]);
                if (min === null || lo < min) min = lo;
                if (max === null || hi > max) max = hi;
            }
        }
        if (min !== null && max !== null) return { min, max };
        const lt = rangeStr.match(/<\s*([\d.]+)/);
        if (lt) return { min: null, max: parseFloat(lt[1]) };
        const gt = rangeStr.match(/>\s*([\d.]+)/);
        if (gt) return { min: parseFloat(gt[1]), max: null };
        return null;
    }

    function detectStatus(value, rangeStr, criticalLow, criticalHigh) {
        const cl = parseFloat(criticalLow), ch = parseFloat(criticalHigh);
        if (!isNaN(cl) && !isNaN(ch) && (value < cl || value > ch)) return 'critical';
        const range = parseRange(rangeStr);
        if (!range || isNaN(value)) return null;
        if (range.max !== null && value > range.max) return 'high';
        if (range.min !== null && value < range.min) return 'low';
        return 'normal';
    }

    function applyBadge(input, select, badge, status) {
        input.classList.remove('border-red-400','border-green-400','border-amber-400','border-purple-500');
        badge.classList.add('hidden');
        badge.className = 'param-status-badge hidden text-[9px] font-bold px-1.5 py-0.5 rounded uppercase';
        if (status === 'critical') {
            input.classList.add('border-purple-500');
            badge.textContent = @json(__('center.results.critical_badge'));
            badge.classList.add('bg-purple-100','text-purple-700','border','border-purple-300');
            badge.classList.remove('hidden');
        } else if (status === 'high') {
            input.classList.add('border-red-400');
            badge.textContent = @json(__('center.results.high_badge'));
            badge.classList.add('bg-red-100','text-red-700','border','border-red-300');
            badge.classList.remove('hidden');
        } else if (status === 'low') {
            input.classList.add('border-amber-400');
            badge.textContent = @json(__('center.results.low_badge'));
            badge.classList.add('bg-amber-100','text-amber-700','border','border-amber-300');
            badge.classList.remove('hidden');
        } else {
            input.classList.add('border-green-400');
            badge.textContent = @json(__('center.results.normal_badge'));
            badge.classList.add('bg-green-100','text-green-700','border','border-green-300');
            badge.classList.remove('hidden');
        }
    }

    document.querySelectorAll('.param-value-input').forEach(input => {
        input.addEventListener('input', function () {
            const idx = this.dataset.index;
            const val = parseFloat(this.value);
            const range = this.dataset.range;
            const select = document.querySelector(`.param-status-select[data-index="${idx}"]`);
            const badge = document.querySelector(`.param-status-badge[data-index="${idx}"]`);
            if (!select || !badge) return;
            if (isNaN(val)) {
                select.value = 'normal';
                this.classList.remove('border-red-400','border-green-400','border-amber-400','border-purple-500');
                badge.classList.add('hidden');
                return;
            }

            const status = detectStatus(val, range, this.dataset.criticalLow, this.dataset.criticalHigh);
            if (status) {
                select.value = status;
                applyBadge(this, select, badge, status);
            }
        });
        if (input.value) input.dispatchEvent(new Event('input'));
    });
});
</script>
@endsection
