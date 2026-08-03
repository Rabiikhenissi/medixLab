<?php

namespace App\Services;

use App\Models\ExamRequest;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Server-side PDF generation for official exam reports (dompdf).
 */
class ExamReportPdf
{
    /**
     * Stream the exam report as a downloadable PDF.
     */
    public static function download(ExamRequest $examRequest)
    {
        $examRequest->load([
            'doctor.user',
            'patient.user',
            'laboratory',
            'items.exam',
            'items.resultLabo.details',
        ]);

        $pdf = Pdf::loadView('patient.exam-report-pdf', ['examRequest' => $examRequest])
            ->setPaper('a4');

        return $pdf->download('rapport-medix-'.$examRequest->id.'.pdf');
    }
}
