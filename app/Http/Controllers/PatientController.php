<?php

namespace App\Http\Controllers;
use App\Models\Labo;
use App\Models\ExamRequest;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\DoctorPatientAccess;
use App\Models\ExamRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Get all notifications for patient
     */
    public function getNotifications()
    {
        $patient = Auth::user();
        $notifications = Notification::forUser($patient->id)
            ->latest('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'type' => $notification->notification_type ?? $this->getNotificationType($notification->message),
                    'reference_id' => $notification->reference_id,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'raw_time' => $notification->created_at,
                ];
            }),
        ]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $patient = Auth::user();
        $count = Notification::forUser($patient->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Get pending access requests for patient
     */
    public function getAccessRequests()
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Profil patient non trouvé.',
                'access_requests' => []
            ], 403);
        }

        $accessRequests = DoctorPatientAccess::where('patient_id', $patient->id)
            ->where('access_status', 'pending')
            ->with('doctor.user')
            ->latest('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'access_requests' => $accessRequests->map(function ($access) {
                return [
                    'id' => $access->id,
                    'doctor_name' => $access->doctor->user->first_name . ' ' . $access->doctor->user->last_name,
                    'doctor_speciality' => $access->doctor->speciality,
                    'created_at' => $access->created_at->format('d/m/Y H:i'),
                    'created_at_relative' => $access->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Handle access request decision (accept/decline)
     */
    public function respondToAccessRequest(Request $request)
    {
        $request->validate([
            'access_id' => 'required|exists:doctor_patient_access,id',
            'action' => 'required|in:accepted,declined,accept,decline',
        ]);

        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Profil patient non trouvé.',
            ], 403);
        }

        $access = DoctorPatientAccess::findOrFail($request->access_id);

        // Verify this access is for the current patient
        if ($access->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Normalize action (accept/accepted -> granted, decline/declined -> revoked)
        $action = $request->action;
        if (in_array($action, ['accept', 'accepted'])) {
            $access->update(['access_status' => 'granted']);
            $message = 'Accès accordé au médecin';
        } else {
            $access->update(['access_status' => 'revoked']);
            $message = 'Demande d\'accès refusée';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get exam requests for patient
     */
    public function getExamRequests()
    {
        $patient = Auth::user()->patient;

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Profil patient non trouvé.',
                'exam_requests' => []
            ], 403);
        }


        $examRequests = ExamRequest::where('patient_id', $patient->id)
            ->with([
                'doctor.user',
                'items.exam',
                'items.resultLabo.details',
                'laboratory'
            ])
            ->latest('created_at')
            ->limit(50)
            ->get();



        return response()->json([
            'success' => true,

            'exam_requests' => $examRequests->map(function ($request) {


                $exams = $request->items->map(function ($item) use ($request) {
                    $showResult = $request->status === 'completed' && $request->approved_by_doctor;
                    return [
                        'id' => $item->exam->id,
                        'name' => $item->exam->name,
                        'category' => $item->exam->category,
                        'description' => $item->exam->description,
                        'result' => ($showResult && $item->resultLabo) ? [
                            'interpretation' => $item->resultLabo->interpretation,
                            'details' => $item->resultLabo->details->map(function ($d) {
                                return [
                                    'parameter' => $d->parameter,
                                    'value' => $d->value,
                                    'status' => $d->status,
                                    'reference_range' => $d->reference_range,
                                ];
                            }),
                        ] : null,
                    ];

                });



                return [

                    'id' => $request->id,


                    'doctor_name' =>
                        $request->doctor->user->first_name
                        . ' '
                        .
                        $request->doctor->user->last_name,


                    'doctor_speciality' =>
                        $request->doctor->speciality,


                    'status' =>
                        $request->status,

                    'approved_by_doctor' =>
                        $request->approved_by_doctor,

                    'doctor_interpretation' =>
                        ($request->status === 'completed' && $request->approved_by_doctor) ? $request->doctor_interpretation : null,


                    'clinical_notes' =>
                        $request->clinical_notes,


                    'laboratory' => $request->laboratory ? [
                        'id' => $request->laboratory->id,
                        'name' => $request->laboratory->name,
                        'city' => $request->laboratory->city,
                    ] : null,


                    'needs_laboratory_selection' =>
                        $request->laboratory_id === null,


                    'exams' => $exams,


                    'exams_count' =>
                        $exams->count(),


                    'created_at' =>
                        $request->created_at->format('d/m/Y H:i'),


                    'created_at_relative' =>
                        $request->created_at->diffForHumans(),

                ];

            }),
        ]);
    }

    /**
     * Get specific exam request details
     */
    public function getExamRequest(ExamRequest $examRequest)
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Profil patient non trouvé.',
            ], 403);
        }

        if ($examRequest->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($examRequest->status === 'completed' && !$examRequest->approved_by_doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Les résultats de vos analyses sont en cours de validation par votre médecin traitant.',
            ], 403);
        }

        // Eager load relationships
        $examRequest->load(['doctor.user', 'items.exam', 'items.resultLabo.details']);

        $exams = $examRequest->items->map(function ($item) {
            return [
                'id' => $item->exam->id,
                'name' => $item->exam->name,
                'category' => $item->exam->category,
                'description' => $item->exam->description,
                'normal_range' => $item->exam->default_normal_range,
                'preparation' => $item->exam->preparation_instructions,
                'result' => $item->resultLabo ? [
                    'interpretation' => $item->resultLabo->interpretation,
                    'details' => $item->resultLabo->details->map(function ($d) {
                        return [
                            'parameter' => $d->parameter,
                            'value' => $d->value,
                            'status' => $d->status,
                            'reference_range' => $d->reference_range,
                        ];
                    }),
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'exam_request' => [
                'id' => $examRequest->id,
                'doctor_name' => $examRequest->doctor->user->first_name . ' ' . $examRequest->doctor->user->last_name,
                'doctor_phone' => $examRequest->doctor->user->phone,
                'doctor_speciality' => $examRequest->doctor->speciality,
                'status' => $examRequest->status,
                'clinical_notes' => $examRequest->clinical_notes,
                'doctor_interpretation' => $examRequest->doctor_interpretation,
                'approved_by_doctor' => $examRequest->approved_by_doctor,
                'exams' => $exams,
                'created_at' => $examRequest->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Determine notification type from message
     */
    private function getNotificationType($message)
    {
        if (strpos($message, 'demande d\'accès') !== false || strpos($message, "d'accès") !== false) {
            return 'access_request';
        } elseif (strpos($message, 'examen') !== false || strpos($message, 'analyse') !== false) {
            return 'exam_request';
        }
        return 'general';
    }
    /**
     * Show laboratories for an exam request
     */
    public function assignLaboratory(Request $request, ExamRequest $examRequest)
    {
        $patient = auth()->user()->patient;

        if (!$patient || $examRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if (!in_array($examRequest->status, ['pending', 'assigned'])) {
            return redirect()
                ->route('patient.dashboard')
                ->with('error', 'Impossible de modifier le laboratoire pour cette prescription.');
        }

        $request->validate([
            'labo_id' => 'required|exists:labos,id'
        ]);

        $examRequest->update([
            'labo_id' => $request->labo_id,
            'status' => 'assigned'
        ]);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Laboratoire sélectionné avec succès.');
    }

    public function chooseLaboratory($id)
    {
        $patient = auth()->user()->patient;

        $examRequest = ExamRequest::with('laboratory')->findOrFail($id);

        if (!$patient || $examRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if (!in_array($examRequest->status, ['pending', 'assigned'])) {
            return redirect()
                ->route('patient.dashboard')
                ->with('error', 'Impossible de modifier le laboratoire pour cette prescription.');
        }

        $laboratories = Labo::with('workingHours')
            ->where('is_archive', false)
            ->orderBy('name')
            ->get();

        return view('patient.choose-laboratory', [
            'examRequest'   => $examRequest,
            'laboratories'  => $laboratories,
        ]);
    }


}
