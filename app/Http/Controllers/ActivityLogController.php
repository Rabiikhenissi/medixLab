<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * List the immutable audit trail with optional filters.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest('id');

        if ($request->filled('entity')) {
            $query->where('entity_type', $request->entity);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%'.$request->search.'%')
                    ->orWhere('entity_type', 'like', '%'.$request->search.'%')
                    ->orWhere('ip_address', 'like', '%'.$request->search.'%');
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $entities = AuditLog::select('entity_type')
            ->whereNotNull('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type');

        return view('admin.activity', compact('logs', 'entities'));
    }
}
