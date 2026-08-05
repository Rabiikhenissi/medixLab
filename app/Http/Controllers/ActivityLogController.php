<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\AuditTrailPdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /** Upper bound on rows included in a single PDF export. */
    private const EXPORT_LIMIT = 5000;

    /**
     * List the immutable audit trail with optional filters.
     */
    public function index(Request $request): View
    {
        $logs = $this->baseQuery($request)->paginate(25)->withQueryString();

        $entities = AuditLog::select('entity_type')
            ->whereNotNull('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type');

        return view('admin.activity', compact('logs', 'entities'));
    }

    /**
     * Stream the filtered audit trail as a PDF export.
     */
    public function export(Request $request)
    {
        $logs = $this->baseQuery($request)
            ->limit(self::EXPORT_LIMIT)
            ->get();

        $filename = 'journal-activite-'.now()->format('Ymd-Hi').'.pdf';

        return AuditTrailPdf::download($logs, $filename);
    }

    /**
     * Build the filtered audit-log query shared by the page and the PDF export.
     */
    private function baseQuery(Request $request)
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

        return $query;
    }
}
