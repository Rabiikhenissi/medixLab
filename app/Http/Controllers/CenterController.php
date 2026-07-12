<?php

namespace App\Http\Controllers;

use App\Models\Labo;
use App\Models\Consumable;
use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use App\Models\WorkingHours;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{
    /**
     * Verify staff group access.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->staff) {
                return redirect()->route('home');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard Overview.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $lab = $user->staff->laboratory;

        // Retrieve statistics
        $stats = [
            'equipment_count' => $lab->equipment()->count(),
            'consumables_count' => $lab->consumables()->count(),
            'low_stock_count' => $lab->consumables()->whereColumn('quantity', '<=', 'min_quantity')->count(),
            'active_maintenance_count' => EquipmentMaintenance::whereIn('equipment_id', $lab->equipment()->pluck('id'))
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        return view('center.dashboard', compact('user', 'stats'));
    }

    /**
     * Working Hours & Exceptions list.
     */
    public function workingHours()
    {
        $lab = auth()->user()->staff->laboratory;

        // Initialize regular weekly working hours if not present
        $regularHours = $lab->workingHours()->whereNull('date_close')->get();
        if ($regularHours->count() === 0) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                WorkingHours::create([
                    'labo_id' => $lab->id,
                    'day' => $day,
                    'start_time' => ($day === 'Sunday' || $day === 'Saturday') ? null : '08:00:00',
                    'end_time' => ($day === 'Sunday' || $day === 'Saturday') ? null : '17:00:00',
                    'is_closed' => ($day === 'Sunday' || $day === 'Saturday') ? true : false,
                ]);
            }
            $regularHours = $lab->workingHours()->whereNull('date_close')->get();
        }

        // Map English days to French labels for rendering
        $dayLabels = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche',
        ];

        // Retrieve exception days off / holidays
        $exceptions = $lab->workingHours()->whereNotNull('date_close')->orderBy('date_close', 'asc')->get();

        return view('center.working-hours', compact('regularHours', 'dayLabels', 'exceptions'));
    }

    /**
     * Update regular working hours.
     */
    public function updateWorkingHours(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        DB::transaction(function () use ($request, $lab, $days) {
            foreach ($days as $day) {
                $isClosed = $request->has("closed_{$day}");
                $startTime = $request->input("start_{$day}");
                $endTime = $request->input("end_{$day}");

                WorkingHours::updateOrCreate(
                    ['labo_id' => $lab->id, 'day' => $day, 'date_close' => null],
                    [
                        'start_time' => $isClosed ? null : $startTime,
                        'end_time' => $isClosed ? null : $endTime,
                        'is_closed' => $isClosed,
                    ]
                );
            }
        });

        return back()->with('success', 'Horaires de travail mis à jour avec succès.');
    }

    /**
     * Add exceptional day off (Holiday).
     */
    public function addException(Request $request)
    {
        $request->validate([
            'date_close' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        $lab = auth()->user()->staff->laboratory;

        // Check if date already exists as an exception
        $exists = WorkingHours::where('labo_id', $lab->id)
            ->where('date_close', $request->input('date_close'))
            ->exists();

        if ($exists) {
            return back()->with('error', 'Cette date figure déjà dans la liste des exceptions.');
        }

        WorkingHours::create([
            'labo_id' => $lab->id,
            'day' => $request->input('reason'), // Use 'day' column to store reason
            'date_close' => $request->input('date_close'),
            'is_closed' => true,
        ]);

        return back()->with('success', 'Exception de fermeture ajoutée avec succès.');
    }

    /**
     * Delete exceptional day off.
     */
    public function deleteException(WorkingHours $workingHour)
    {
        if ($workingHour->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $workingHour->delete();

        return back()->with('success', 'Exception supprimée avec succès.');
    }

    /**
     * View consumables and movements list.
     */
    public function consumables(Request $request)
    {
        $search = $request->input('search', '');
        $lab = auth()->user()->staff->laboratory;

        $query = $lab->consumables();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $consumables = $query->orderBy('name', 'asc')->paginate(10, ['*'], 'consumables_page')->appends($request->query());

        // Get movements log for history tab
        $movements = StockMovement::whereIn('consumable_id', $lab->consumables()->pluck('id'))
            ->with('consumable')
            ->latest()
            ->paginate(10, ['*'], 'movements_page')
            ->appends($request->query());

        return view('center.consumables', compact('consumables', 'search', 'movements'));
    }

    /**
     * Store new consumable.
     */
    public function storeConsumable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
        ]);

        $lab = auth()->user()->staff->laboratory;

        Consumable::create([
            'labo_id' => $lab->id,
            'name' => $request->input('name'),
            'unit' => $request->input('unit'),
            'quantity' => $request->input('quantity'),
            'min_quantity' => $request->input('min_quantity'),
            'is_archive' => false,
        ]);

        return back()->with('success', 'Consommable ajouté avec succès.');
    }

    /**
     * Update consumable details.
     */
    public function updateConsumable(Request $request, Consumable $consumable)
    {
        if ($consumable->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'min_quantity' => 'required|integer|min:0',
        ]);

        $consumable->update([
            'name' => $request->input('name'),
            'unit' => $request->input('unit'),
            'min_quantity' => $request->input('min_quantity'),
        ]);

        return back()->with('success', 'Consommable mis à jour avec succès.');
    }

    /**
     * Add stock movement.
     */
    public function addStockMovement(Request $request, Consumable $consumable)
    {
        if ($consumable->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:in,out',
            'quantity_change' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $type = $request->input('type');
        $qtyChange = (int)$request->input('quantity_change');

        DB::transaction(function () use ($consumable, $type, $qtyChange, $request) {
            // Create stock movement
            StockMovement::create([
                'consumable_id' => $consumable->id,
                'quantity_change' => $qtyChange,
                'type' => $type,
                'reason' => $request->input('reason'),
            ]);

            // Adjust consumable quantity
            if ($type === 'in') {
                $consumable->quantity += $qtyChange;
            } else {
                $consumable->quantity = max(0, $consumable->quantity - $qtyChange);
            }
            $consumable->save();
        });

        return back()->with('success', 'Mouvement de stock enregistré avec succès.');
    }

    /**
     * View equipment and maintenance logs.
     */
    public function equipment(Request $request)
    {
        $search = $request->input('search', '');
        $lab = auth()->user()->staff->laboratory;

        $query = $lab->equipment();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $equipment = $query->orderBy('name', 'asc')->paginate(10, ['*'], 'equipment_page')->appends($request->query());

        // Get maintenance logs
        $maintenances = EquipmentMaintenance::whereIn('equipment_id', $lab->equipment()->pluck('id'))
            ->with(['equipment', 'staff.user'])
            ->latest()
            ->paginate(10, ['*'], 'maintenance_page')
            ->appends($request->query());

        return view('center.equipment', compact('equipment', 'search', 'maintenances'));
    }

    /**
     * Store new equipment.
     */
    public function storeEquipment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance,retired',
        ]);

        $lab = auth()->user()->staff->laboratory;

        Equipment::create([
            'labo_id' => $lab->id,
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'serial_number' => $request->input('serial_number'),
            'status' => $request->input('status'),
            'is_archive' => false,
        ]);

        return back()->with('success', 'Équipement ajouté avec succès.');
    }

    /**
     * Update equipment details.
     */
    public function updateEquipment(Request $request, Equipment $equipment)
    {
        if ($equipment->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance,retired',
        ]);

        $equipment->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'serial_number' => $request->input('serial_number'),
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Store equipment maintenance report.
     */
    public function storeMaintenance(Request $request, Equipment $equipment)
    {
        if ($equipment->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'issue_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $status = $request->input('status');

        DB::transaction(function () use ($equipment, $status, $request) {
            EquipmentMaintenance::create([
                'equipment_id' => $equipment->id,
                'staff_id' => auth()->user()->staff->id,
                'issue_type' => $request->input('issue_type'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $status,
            ]);

            // Update equipment status automatically
            if ($status === 'pending' || $status === 'in_progress') {
                $equipment->update(['status' => 'maintenance']);
            } elseif ($status === 'completed') {
                $equipment->update(['status' => 'active']);
            }
        });

        return back()->with('success', 'Rapport de maintenance créé avec succès.');
    }

    /**
     * Update equipment maintenance log status.
     */
    public function updateMaintenance(Request $request, EquipmentMaintenance $maintenance)
    {
        if ($maintenance->equipment->labo_id !== auth()->user()->staff->laboratory_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'end_date' => 'nullable|date|after_or_equal:' . $maintenance->start_date->format('Y-m-d H:i:s'),
        ]);

        $status = $request->input('status');

        DB::transaction(function () use ($maintenance, $status, $request) {
            $maintenance->update([
                'status' => $status,
                'end_date' => $request->input('end_date') ?? ($status === 'completed' ? now() : $maintenance->end_date),
                'staff_id' => auth()->user()->staff->id, // Last staff who modified it
            ]);

            // Sync equipment status
            $equipment = $maintenance->equipment;
            if ($status === 'pending' || $status === 'in_progress') {
                $equipment->update(['status' => 'maintenance']);
            } elseif ($status === 'completed') {
                $equipment->update(['status' => 'active']);
            }
        });

        return back()->with('success', 'État de maintenance mis à jour avec succès.');
    }
}
