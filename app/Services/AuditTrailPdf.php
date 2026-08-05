<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

/**
 * Server-side PDF generation for the immutable audit trail (dompdf).
 */
class AuditTrailPdf
{
    /**
     * Stream the audit trail as a downloadable PDF.
     *
     * @param  Collection  $logs  audit log entries (with their user relation)
     */
    public static function download(Collection $logs, string $filename = 'journal-activite.pdf')
    {
        $pdf = Pdf::loadView('admin.activity-pdf', [
            'logs' => $logs,
            'generatedBy' => auth()->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
