@extends('layouts.center')

@section('title', __('center.equipment.title').' - Medix eSanté')

@section('content')
    <!-- Tabs Header -->
    <div class="flex space-x-2 border-b border-[#e2e8f0]/60 pb-3 mb-6 select-none">
        <button onclick="switchTab('equipment')" id="tab-btn-equipment" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]">
            {{ __('center.equipment.inventory_tab') }}
        </button>
        <button onclick="switchTab('maintenance')" id="tab-btn-maintenance" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]">
            {{ __('center.equipment.maintenance_tab') }}
        </button>
    </div>

    <!-- ──────────────── TAB: EQUIPMENT ──────────────── -->
    <div id="tab-content-equipment" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Search -->
            <form method="GET" action="{{ route('center.equipment') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('center.equipment.search_placeholder') }}" class="pl-9 pr-4 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/20 focus:border-[#7C3AED] w-full sm:w-64 text-[#1e293b] bg-white shadow-3xs">
                </div>
                <button type="submit" class="bg-[#7C3AED] hover:bg-[#5B21B6] text-white font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider shadow-md shadow-[#7C3AED]/20 transition cursor-pointer">
                    {{ __('common.filter') }}
                </button>
            </form>
            <button onclick="openEquipmentModal()" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-wider shadow-md shadow-[#7C3AED]/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('center.equipment.new') }}
            </button>
        </div>

        <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-x-auto shadow-xs">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('common.name') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.consumables.type') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.equipment.serial_number') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">{{ __('common.status') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($equipment as $item)
                        @php
                            // Map status to display values
                            $statusMap = [
                                'pending' => ['label' => __('center.equipment.status_pending'), 'color' => 'bg-red-50 text-red-600 border border-red-200'],
                                'in_progress' => ['label' => __('center.equipment.status_in_progress'), 'color' => 'bg-amber-50 text-amber-600 border border-amber-200'],
                                'completed' => ['label' => __('center.equipment.status_completed'), 'color' => 'bg-emerald-50 text-emerald-600 border border-emerald-200'],
                                'cancelled' => ['label' => __('center.equipment.status_cancelled'), 'color' => 'bg-gray-50 text-gray-600 border border-gray-200'],
                            ];
                            $statusInfo = $statusMap[$item->status] ?? ['label' => ucfirst($item->status), 'color' => 'bg-gray-50 text-gray-600 border border-gray-200'];
                        @endphp
                        <tr class="hover:bg-[#F8FAFC]/50 transition">
                            <td class="p-4 font-bold text-[#1e293b]">{{ $item->name }}</td>
                            <td class="p-4 text-[#475569]">{{ $item->type }}</td>
                            <td class="p-4 text-[#475569]">{{ $item->serial_number }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold {{ $statusInfo['color'] }} uppercase tracking-wider">
                                    {{ $statusInfo['label'] }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex gap-2">
                                    <button onclick="openMaintenanceModal({{ $item->id }}, '{{ addslashes($item->name) }}')" class="px-3 py-1.5 bg-[#7C3AED]/10 hover:bg-[#7C3AED]/20 text-[#7C3AED] font-bold text-xs rounded-lg transition" title="{{ __('center.equipment.maintenance') }}">
                                        {{ __('center.equipment.maintenance') }}
                                    </button>
                                    <button onclick="openEditEquipmentModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->type) }}', '{{ $item->serial_number }}', '{{ $item->status }}')" class="p-1.5 border border-[#e2e8f0] text-gray-400 hover:text-[#7C3AED] hover:border-[#7C3AED]/30 rounded-lg transition" title="{{ __('common.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">{{ __('center.equipment.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($equipment->hasPages())
                <div class="px-4 py-3 bg-[#F8FAFC] border-t border-[#e2e8f0]/80">
                    {{ $equipment->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ──────────────── TAB: MAINTENANCE HISTORY ──────────────── -->
    <div id="tab-content-maintenance" class="space-y-6 hidden">
        <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-x-auto shadow-xs">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-[#F8FAFC]/80 border-b border-[#e2e8f0]/80">
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.equipment.start_date') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.equipment.end_date') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.equipment.equipment') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('common.status') }}</th>
                        <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">{{ __('center.equipment.description') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($maintenances as $log)
                        @php
                            $statusInfo = $statusMap[$log->status] ?? ['label' => ucfirst($log->status), 'color' => 'bg-gray-50 text-gray-600 border border-gray-200'];
                        @endphp
                        <tr class="hover:bg-[#F8FAFC]/50 transition">
                            <td class="p-4 text-xs font-medium text-[#475569]">{{ optional($log->start_date)->format(__('center.date_format')) ?? '-' }}</td>
                            <td class="p-4 text-xs font-medium text-[#475569]">{{ optional($log->end_date)->format(__('center.date_format')) ?? '-' }}</td>
                            <td class="p-4 font-bold text-[#1e293b]">{{ $log->equipment->name }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold {{ $statusInfo['color'] }} uppercase tracking-wider">
                                    {{ $statusInfo['label'] }}
                                </span>
                            </td>
                            <td class="p-4 text-[#475569]">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">{{ __('center.equipment.empty_maintenance') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($maintenances->hasPages())
                <div class="px-4 py-3 bg-[#F8FAFC] border-t border-[#e2e8f0]/80">
                    {{ $maintenances->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ──────────────── MODAL: ADD EQUIPMENT ──────────────── -->
    <div id="equipment-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeEquipmentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">{{ __('center.equipment.add_title') }}</h3>
            <form action="{{ route('center.equipment.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.name_label') }}</label>
                    <input type="text" name="name" id="name" required placeholder="{{ __('center.equipment.name_placeholder') }}" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="type" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.type_category') }}</label>
                    <input type="text" name="type" id="type" required placeholder="{{ __('center.equipment.type_placeholder') }}" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="serial_number" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.serial_number') }}</label>
                    <input type="text" name="serial_number" id="serial_number" required placeholder="{{ __('center.equipment.serial_placeholder') }}" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeEquipmentModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">{{ __('common.cancel') }}</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">{{ __('center.consumables.create') }}</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- ──────────────── MODAL: EDIT EQUIPMENT ──────────────── -->
    <div id="edit-equipment-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeEditEquipmentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">{{ __('center.equipment.edit_title') }}</h3>
            <form id="edit-equipment-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.name_label') }}</label>
                    <input type="text" name="name" id="edit_name" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="edit_type" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.type_category') }}</label>
                    <input type="text" name="type" id="edit_type" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="edit_serial_number" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.serial_number') }}</label>
                    <input type="text" name="serial_number" id="edit_serial_number" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="edit_status" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('common.status') }}</label>
                    <select name="status" id="edit_status" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                        <option value="pending">{{ __('center.equipment.option_pending') }}</option>
                        <option value="in_progress">{{ __('center.equipment.option_in_progress') }}</option>
                        <option value="completed">{{ __('center.equipment.option_completed') }}</option>
                        <option value="cancelled">{{ __('center.equipment.option_cancelled') }}</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeEditEquipmentModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">{{ __('common.cancel') }}</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">{{ __('common.save') }}</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- ──────────────── MODAL: MAINTENANCE LOG ──────────────── -->
    <div id="maintenance-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative mx-4">
            <button onclick="closeMaintenanceModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">{{ __('center.equipment.maintenance_log_title') }}</h3>
            <form id="maintenance-form" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.equipment') }}</label>
                    <div id="maintenance-equipment-name" class="mt-1 text-sm font-bold text-[#7C3AED]"></div>
                </div>
                <div>
                    <label for="maintenance_status" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('common.status') }}</label>
                    <select name="status" id="maintenance_status" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                        <option value="pending">{{ __('center.equipment.option_pending') }}</option>
                        <option value="in_progress">{{ __('center.equipment.option_in_progress') }}</option>
                        <option value="completed">{{ __('center.equipment.option_completed') }}</option>
                        <option value="cancelled">{{ __('center.equipment.option_cancelled') }}</option>
                    </select>
                </div>
                <div>
                    <label for="maintenance_start" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.start_date') }}</label>
                    <input type="date" name="start_date" id="maintenance_start" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="maintenance_end" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.end_date_optional') }}</label>
                    <input type="date" name="end_date" id="maintenance_end" class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]">
                </div>
                <div>
                    <label for="maintenance_description" class="text-xs font-bold text-[#475569] uppercase tracking-wider">{{ __('center.equipment.description_notes') }}</label>
                    <textarea name="description" id="maintenance_description" rows="3" required class="w-full mt-1 px-3 py-2 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] bg-white text-[#1e293b]"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeMaintenanceModal()" class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 uppercase tracking-wider">{{ __('common.cancel') }}</button>
                    <x-button type="submit" color="purple" :fullWidth="false" class="!py-2 !px-5 !text-xs font-bold uppercase tracking-wider">{{ __('common.save') }}</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Tab logic
    function switchTab(tab) {
        const equipmentTab = document.getElementById('tab-content-equipment');
        const maintenanceTab = document.getElementById('tab-content-maintenance');
        const equipmentBtn = document.getElementById('tab-btn-equipment');
        const maintenanceBtn = document.getElementById('tab-btn-maintenance');
        if (tab === 'equipment') {
            equipmentTab.classList.remove('hidden');
            maintenanceTab.classList.add('hidden');
            equipmentBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]";
            maintenanceBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]";
        } else {
            equipmentTab.classList.add('hidden');
            maintenanceTab.classList.remove('hidden');
            equipmentBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 text-[#64748b] hover:text-[#1e293b]";
            maintenanceBtn.className = "px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-150 bg-[#7C3AED]/10 text-[#7C3AED]";
        }
    }

    // Equipment modal
    function openEquipmentModal() {
        const modal = document.getElementById('equipment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeEquipmentModal() {
        const modal = document.getElementById('equipment-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Edit equipment modal
    function openEditEquipmentModal(id, name, type, serial, status) {
        const modal = document.getElementById('edit-equipment-modal');
        const form = document.getElementById('edit-equipment-form');
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_serial_number').value = serial;
        document.getElementById('edit_status').value = status;
        form.action = '/center/equipment/' + id;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeEditEquipmentModal() {
        const modal = document.getElementById('edit-equipment-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Maintenance modal
    function openMaintenanceModal(id, name) {
        const modal = document.getElementById('maintenance-modal');
        const form = document.getElementById('maintenance-form');
        document.getElementById('maintenance-equipment-name').textContent = name;
        form.action = '/center/equipment/' + id + '/maintenance';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeMaintenanceModal() {
        const modal = document.getElementById('maintenance-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Auto switch to maintenance tab on return from pagination
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('maintenance_page')) {
            switchTab('maintenance');
        }
    });
</script>
@endsection
