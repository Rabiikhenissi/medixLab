@extends('layouts.center')

@section('title', 'Gestion du Stock - Medix eSanté')

@section('content')
    <!-- Tabs Header -->
    <div class="flex space-x-2 border-b border-[#e2e8f0]/60 pb-3 mb-6 select-none">
        <button onclick="switchTab('stock')" id="tab-btn-stock" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]">
            Stock des Consommables
        </button>
        <button onclick="switchTab('history')" id="tab-btn-history" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]">
            Historique des Mouvements
        </button>
    </div>

    <!-- ──────────────── TAB: STOCK ──────────────── -->
    <div id="tab-content-stock" class="space-y-6">
        
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Search -->
            <form method="GET" action="{{ route('center.consumables') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher consommable..." class="pl-9 pr-4 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] w-64 text-[#1e293b] bg-white shadow-3xs">
                </div>
                <button type="submit" class="bg-[#7C3AED] hover:bg-[#5B21B6] text-white font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider shadow-md shadow-[#7C3AED]/20 transition cursor-pointer">
                    Filtrer
                </button>
            </form>

            <!-- Add Consumable button -->
            <button onclick="openConsumableModal()" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider shadow-md shadow-[#7C3AED]/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouveau Consommable
            </button>
        </div>

        <!-- Consumables Table -->
        <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Désignation</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Quantité En Stock</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Stock Minimum</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Statut Alerte</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($consumables as $item)
                        @php
                            $isLowStock = $item->quantity <= $item->min_quantity;
                        @endphp
                        <tr class="hover:bg-[#F8FAFC]/50 transition">
                            <td class="p-4">
                                <div class="font-bold text-[#1e293b]">{{ $item->name }}</div>
                                <div class="text-[10px] font-bold text-[#64748b] mt-0.5 uppercase">Unité : {{ $item->unit }}</div>
                            </td>
                            <td class="p-4 text-center font-bold text-[#1e293b]">
                                {{ $item->quantity }} <span class="text-xs text-[#64748b] font-medium">{{ $item->unit }}</span>
                            </td>
                            <td class="p-4 text-center font-semibold text-[#475569]">
                                {{ $item->min_quantity }} <span class="text-xs text-gray-400 font-medium">{{ $item->unit }}</span>
                            </td>
                            <td class="p-4 text-center select-none">
                                @if($isLowStock)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold text-red-600 bg-red-50 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Rupture / Stock Bas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Stock Suffisant
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex gap-2">
                                    <!-- Movement Button -->
                                    <button onclick="openMovementModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit }}')" class="px-3 py-1.5 bg-[#7C3AED]/10 hover:bg-[#7C3AED]/20 text-[#7C3AED] font-bold text-xs rounded-lg transition" title="Mouvement Stock">
                                        Mouvement
                                    </button>
                                    
                                    <!-- Edit Button -->
                                    <button onclick="openEditConsumableModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->unit) }}', {{ $item->min_quantity }})" class="p-1.5 border border-[#e2e8f0] text-gray-400 hover:text-[#7C3AED] hover:border-[#7C3AED]/30 rounded-lg transition" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                Aucun consommable enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($consumables->hasPages())
                <div class="px-4 py-3 bg-[#F8FAFC] border-t border-[#e2e8f0]/80">
                    {{ $consumables->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ──────────────── TAB: HISTORY ──────────────── -->
    <div id="tab-content-history" class="space-y-6 hidden">
        <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Date & Heure</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Consommable</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Type</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Quantité</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Motif / Justification</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($movements as $log)
                        <tr class="hover:bg-[#F8FAFC]/50 transition">
                            <td class="p-4 text-xs font-semibold text-[#64748b]">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-bold text-[#1e293b]">
                                {{ $log->consumable->name }}
                            </td>
                            <td class="p-4 text-center select-none">
                                @if($log->type === 'in')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                                        Entrée (+)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase tracking-wider">
                                        Sortie (-)
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-bold {{ $log->type === 'in' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $log->type === 'in' ? '+' : '-' }}{{ $log->quantity_change }} <span class="text-xs text-[#64748b] font-medium">{{ $log->consumable->unit }}</span>
                            </td>
                            <td class="p-4 text-[#475569] font-medium">
                                {{ $log->reason }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                Aucun mouvement de stock enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($movements->hasPages())
                <div class="px-4 py-3 bg-[#F8FAFC] border-t border-[#e2e8f0]/80">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ──────────────── MODAL: ADD CONSUMABLE ──────────────── -->
    <div id="consumable-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeConsumableModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Ajouter un Consommable</h3>
            
            <form action="{{ route('center.consumables.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Nom du consommable</label>
                    <input type="text" name="name" id="name" required placeholder="Ex: Aiguilles de prélèvement" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="unit" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Unité de mesure</label>
                    <input type="text" name="unit" id="unit" required placeholder="Ex: pièces, flacons, boîtes" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Quantité Initiale</label>
                        <input type="number" name="quantity" id="quantity" min="0" required placeholder="0" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                    </div>
                    <div>
                        <label for="min_quantity" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Alerte Stock Bas (min)</label>
                        <input type="number" name="min_quantity" id="min_quantity" min="0" required placeholder="10" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeConsumableModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">Annuler</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">Créer</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- ──────────────── MODAL: EDIT CONSUMABLE ──────────────── -->
    <div id="edit-consumable-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeEditConsumableModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Modifier un Consommable</h3>
            
            <form id="edit-consumable-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Nom du consommable</label>
                    <input type="text" name="name" id="edit_name" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="edit_unit" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Unité de mesure</label>
                    <input type="text" name="unit" id="edit_unit" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="edit_min_quantity" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Alerte Stock Bas (min)</label>
                    <input type="number" name="min_quantity" id="edit_min_quantity" min="0" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeEditConsumableModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">Annuler</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">Enregistrer</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- ──────────────── MODAL: REGISTER STOCK MOVEMENT ──────────────── -->
    <div id="movement-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeMovementModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Enregistrer un Mouvement de Stock</h3>
            
            <form id="movement-form" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-[#475569] uppercase tracking-wider">Consommable</label>
                    <div id="modal-consumable-name" class="mt-1 text-sm font-bold text-[#7C3AED]"></div>
                </div>
                <div>
                    <label for="move_type" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Type de Mouvement</label>
                    <select name="type" id="move_type" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                        <option value="in">Entrée (+) [Réapprovisionnement, Don]</option>
                        <option value="out">Sortie (-) [Utilisation, Casse/Périmé]</option>
                    </select>
                </div>
                <div>
                    <label for="move_qty" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Quantité Modifiée (<span id="modal-consumable-unit"></span>)</label>
                    <input type="number" name="quantity_change" id="move_qty" min="1" required placeholder="Ex: 50" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="move_reason" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Motif / Réf. (Reason)</label>
                    <input type="text" name="reason" id="move_reason" required placeholder="Ex: Utilisation prélèvement, Restock" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeMovementModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">Annuler</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">Enregistrer</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Tab switching logic
    function switchTab(tabId) {
        var stockTab = document.getElementById('tab-content-stock');
        var historyTab = document.getElementById('tab-content-history');
        var stockBtn = document.getElementById('tab-btn-stock');
        var historyBtn = document.getElementById('tab-btn-history');

        if (tabId === 'stock') {
            stockTab.classList.remove('hidden');
            historyTab.classList.add('hidden');
            stockBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]";
            historyBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]";
        } else {
            stockTab.classList.add('hidden');
            historyTab.classList.remove('hidden');
            stockBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]";
            historyBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]";
        }
    }

    // Modal Add Consumable
    function openConsumableModal() {
        var modal = document.getElementById('consumable-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeConsumableModal() {
        var modal = document.getElementById('consumable-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Modal Edit Consumable
    function openEditConsumableModal(id, name, unit, minQty) {
        var modal = document.getElementById('edit-consumable-modal');
        var form = document.getElementById('edit-consumable-form');
        
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_unit').value = unit;
        document.getElementById('edit_min_quantity').value = minQty;
        
        form.action = '/center/consumables/' + id;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeEditConsumableModal() {
        var modal = document.getElementById('edit-consumable-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Modal Stock Movement
    function openMovementModal(id, name, unit) {
        var modal = document.getElementById('movement-modal');
        var form = document.getElementById('movement-form');
        
        document.getElementById('modal-consumable-name').textContent = name;
        document.getElementById('modal-consumable-unit').textContent = unit;
        document.getElementById('move_qty').value = '';
        document.getElementById('move_reason').value = '';
        
        form.action = '/center/consumables/' + id + '/move';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeMovementModal() {
        var modal = document.getElementById('movement-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    
    // Automatically switch tabs if returning from pagination or filtering of movements
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('movements_page')) {
            switchTab('history');
        }
    });
</script>
@endsection
