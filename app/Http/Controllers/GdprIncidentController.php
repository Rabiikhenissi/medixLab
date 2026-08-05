<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\GdprIncident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GdprIncidentController extends Controller
{
    /** List the incident register with open incidents first. */
    public function index(Request $request): View
    {
        $incidents = GdprIncident::query()
            ->orderBy('status')
            ->orderByDesc('detected_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr-incidents', compact('incidents'));
    }

    /** Record a new incident / data breach in the register. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'incident_type' => 'required|string|max:50',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
            'affected_users_count' => 'nullable|integer|min:0',
            'detected_at' => 'required|date',
            'notified_authority_at' => 'nullable|date',
            'notified_affected_at' => 'nullable|date',
        ]);

        $incident = GdprIncident::create($data + ['status' => 'open']);

        AuditLog::create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'action' => 'gdpr-incident-report',
            'entity_type' => 'GdprIncident',
            'entity_id' => $incident->id,
            'description' => 'Incident RGPD déclaré ('.$incident->severity.'): '.$incident->incident_type,
            'changes' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.gdpr.incidents')
            ->with('success', 'Incident enregistré dans le registre.');
    }

    /** Resolve an open incident. */
    public function resolve(Request $request, GdprIncident $incident): RedirectResponse
    {
        $data = $request->validate([
            'resolution' => 'required|string',
        ]);

        $incident->update([
            'resolution' => $data['resolution'],
            'status' => 'resolved',
        ]);

        return redirect()
            ->route('admin.gdpr.incidents')
            ->with('success', 'Incident #'.$incident->id.' marqué comme résolu.');
    }
}
