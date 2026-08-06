@extends('layouts.center')

@section('title', __('center.billing.title').' - Medix eSanté')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 select-none">
    <div>
        <h1 class="text-3xl font-bold text-[#1e293b]">{{ __('center.billing.create_title') }}</h1>
        <p class="text-sm text-[#64748b] mt-2">{{ __('center.billing.create_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-semibold">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('center.billing.store') }}" id="billingForm">
        @csrf

        <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 space-y-4">
            <div>
                <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.billing.exam_request_label') }}</label>
                <select name="exam_request_id" id="examRequestSelect" required class="mt-1 w-full border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm focus:border-[#7C3AED] focus:ring-1 focus:ring-[#7C3AED] outline-none">
                    <option value="">{{ __('center.billing.select_completed') }}</option>
                    @foreach($completedRequests as $req)
                        <option value="{{ $req->id }}" data-items='@json($req->items->map(fn($i) => ["id" => $i->id, "exam" => $i->exam->name ?? "N/A", "material" => $i->material_type]))'>
                            #{{ $req->id }} - {{ $req->patient->user->first_name }} {{ $req->patient->user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="itemsContainer" class="mt-4 space-y-3">
            <p class="text-sm text-[#94a3b8]">{{ __('center.billing.select_first') }}</p>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-6 py-2.5 rounded-xl text-sm shadow-md transition">
                {{ __('center.billing.create_button') }}
            </button>
            <a href="{{ route('center.billing.index') }}" class="text-[#64748b] hover:text-[#1e293b] font-medium px-4 py-2.5 text-sm">{{ __('common.cancel') }}</a>
        </div>
    </form>
</div>

<script>
document.getElementById('examRequestSelect').addEventListener('change', function() {
    const container = document.getElementById('itemsContainer');
    const selected = this.options[this.selectedIndex];
    if (!selected || !selected.value) {
        container.innerHTML = '<p class="text-sm text-[#94a3b8]">' + @json(__('center.billing.select_first')) + '</p>';
        return;
    }

    let items = [];
    try { items = JSON.parse(selected.dataset.items); } catch(e) {}

    if (items.length === 0) {
        container.innerHTML = '<p class="text-sm text-[#94a3b8]">' + @json(__('center.billing.no_items')) + '</p>';
        return;
    }

    let html = '<div class="bg-white border border-[#e2e8f0] rounded-2xl p-6"><h3 class="text-xs font-bold text-[#64748b] uppercase tracking-wider mb-4">' + @json(__('center.billing.items_title')) + '</h3>';
    items.forEach((item, idx) => {
        html += `
            <div class="border-b border-[#e2e8f0]/60 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                <input type="hidden" name="items[${idx}][exam_request_item_id]" value="${item.id}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-bold text-sm text-[#1e293b]">${item.exam}</span>
                    <span class="text-[10px] text-[#64748b] bg-slate-100 px-2 py-0.5 rounded-full">${item.material || 'N/A'}</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase">${@json(__('center.billing.description'))}</label>
                        <input type="text" name="items[${idx}][description]" value="${item.exam}" required class="mt-1 w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-xs focus:border-[#7C3AED] outline-none">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase">${@json(__('center.billing.qty'))}</label>
                        <input type="number" name="items[${idx}][quantity]" value="1" min="1" required class="mt-1 w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-xs focus:border-[#7C3AED] outline-none">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase">${@json(__('center.billing.unit_price_tnd'))}</label>
                        <input type="number" step="0.001" name="items[${idx}][unit_price]" value="0" min="0" required class="mt-1 w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-xs focus:border-[#7C3AED] outline-none">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-[#64748b] uppercase">${@json(__('center.billing.code_cnam'))}</label>
                        <select name="items[${idx}][cnam_code]" class="mt-1 w-full border border-[#e2e8f0] rounded-lg px-3 py-2 text-xs focus:border-[#7C3AED] outline-none">
                            <option value="">${@json(__('center.billing.without_cnam'))}</option>
                            @foreach($nomenclatures as $n)
                                <option value="{{ $n->code_cnam }}" data-vb="{{ $n->valeur_b }}">{{ $n->code_cnam }} - {{ $n->exam_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hidden">
                        <input type="number" step="0.001" name="items[${idx}][valeur_b]" value="0">
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
});
</script>
@endsection
