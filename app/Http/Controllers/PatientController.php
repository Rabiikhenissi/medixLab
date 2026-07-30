<?php

namespace App\Http\Controllers;
use App\Models\ExamRequest;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\DoctorPatientAccess;
use App\Models\Labo;
use App\Services\LabRecommendationService;
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
     * Mark all notifications as read for the authenticated patient
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->where('is_archive', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications marquées comme lues.',
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
     * Get granted doctors for patient (so patient can revoke/block)
     */
    public function getGrantedDoctors()
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return response()->json(['success' => false, 'granted' => []], 403);
        }

        $granted = DoctorPatientAccess::where('patient_id', $patient->id)
            ->where('access_status', 'granted')
            ->with('doctor.user')
            ->latest('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'granted' => $granted->map(function ($access) {
                return [
                    'access_id'    => $access->id,
                    'doctor_id'    => $access->doctor_id,
                    'doctor_name'  => $access->doctor->user->first_name . ' ' . $access->doctor->user->last_name,
                    'speciality'   => $access->doctor->speciality ?? '',
                    'expires_at'   => $access->expires_at ? $access->expires_at->format('d/m/Y') : null,
                    'granted_at'   => $access->updated_at->diffForHumans(),
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

        // Normalize action and set expiry on grant
        $action = $request->action;
        if (in_array($action, ['accept', 'accepted'])) {
            $access->update([
                'access_status' => 'granted',
                'expires_at'    => now()->addMonths(6),
            ]);
            $message = 'Accès accordé au médecin (valide 6 mois)';
        } else {
            $access->update(['access_status' => 'revoked']);
            $message = 'Demande d\'accès refusée';
        }

        if ($request->has('notification_id')) {
            Notification::where('id', $request->notification_id)
                ->where('user_id', $user->id)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Revoke doctor access
     */
    public function revokeAccess(Request $request)
    {
        $request->validate([
            'access_id' => 'required|exists:doctor_patient_access,id',
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

        if ($access->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $access->update(['access_status' => 'revoked']);

        // Create notification for doctor
        Notification::create([
            'user_id' => $access->doctor->user->id,
            'title' => 'Accès révoqué',
            'message' => 'Le patient ' . $user->first_name . ' ' . $user->last_name . ' a révoqué votre accès à son dossier médical.',
            'notification_type' => 'general',
            'reference_id' => $access->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Accès révoqué avec succès.',
        ]);
    }

    /**
     * Block a doctor — doctor can no longer see this patient in search or past patients
     */
    public function blockDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $user    = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Profil patient non trouvé.'], 403);
        }

        $access = DoctorPatientAccess::where('doctor_id', $request->doctor_id)
            ->where('patient_id', $patient->id)
            ->first();

        if ($access) {
            $access->update(['access_status' => 'blocked']);
        } else {
            DoctorPatientAccess::create([
                'doctor_id'     => $request->doctor_id,
                'patient_id'    => $patient->id,
                'access_status' => 'blocked',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Médecin bloqué avec succès.',
        ]);
    }

    /**
     * Unblock a doctor
     */
    public function unblockDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $user    = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Profil patient non trouvé.'], 403);
        }

        $access = DoctorPatientAccess::where('doctor_id', $request->doctor_id)
            ->where('patient_id', $patient->id)
            ->where('access_status', 'blocked')
            ->first();

        if ($access) {
            $access->update(['access_status' => 'granted', 'expires_at' => now()->addMonths(6)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Médecin débloqué avec succès.',
        ]);
    }

    /**
     * Get list of blocked doctors
     */
    public function getBlockedDoctors()
    {
        $user    = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            return response()->json(['success' => false, 'access_requests' => []], 403);
        }

        $blocked = DoctorPatientAccess::where('patient_id', $patient->id)
            ->where('access_status', 'blocked')
            ->with('doctor.user')
            ->get();

        return response()->json([
            'success' => true,
            'blocked' => $blocked->map(function ($access) {
                return [
                    'id'          => $access->id,
                    'doctor_id'   => $access->doctor_id,
                    'doctor_name' => $access->doctor->user->first_name . ' ' . $access->doctor->user->last_name,
                    'speciality'  => $access->doctor->speciality ?? '',
                ];
            }),
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

    public function chooseLaboratory(ExamRequest $examRequest)
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

        $requiredExamIds = $examRequest->items->pluck('exam_id')->toArray();

        $laboratories = Labo::with(['workingHours', 'availableExams.exam'])
            ->where('is_archive', false)
            ->orderBy('name')
            ->get();

        $recommendationService = new LabRecommendationService(
            $patient->latitude ?? null,
            $patient->longitude ?? null,
            $requiredExamIds
        );

        $rankedLabs = $recommendationService->rankLabs($laboratories);
        $availabilityMap = $recommendationService->getAvailabilityMap();

        return view('patient.choose-laboratory', [
            'examRequest'     => $examRequest,
            'laboratories'    => $laboratories,
            'requiredExamIds' => $requiredExamIds,
            'rankedLabs'      => $rankedLabs,
            'availabilityMap' => $availabilityMap,
        ]);
    }


    /**
     * Print/PDF export of a completed exam request (Task 3.2)
     */
    public function printExamRequest(ExamRequest $examRequest)
    {
        $patient = auth()->user()->patient;

        if (!$patient || $examRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if ($examRequest->status !== 'completed' || !$examRequest->approved_by_doctor) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Le rapport n\'est disponible que pour les demandes complétées et approuvées.');
        }

        $examRequest->load(['doctor.user', 'patient.user', 'laboratory', 'items.exam', 'items.resultLabo.details']);

        return view('patient.print-exam-request', compact('examRequest'));
    }

    /**
     * Medical history timeline for the patient (Task 3.5)
     */
    public function medicalHistory()
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            return redirect()->route('patient.dashboard');
        }

        $examRequests = ExamRequest::where('patient_id', $patient->id)
            ->with([
                'doctor.user',
                'laboratory',
                'items.exam',
                'items.resultLabo.details',
            ])
            ->latest('created_at')
            ->get();

        return view('patient.medical-history', [
            'user'         => auth()->user(),
            'patient'      => $patient,
            'examRequests' => $examRequests,
        ]);
    }

    /**
     * Cancel an exam request (only if not completed)
     */
    public function cancelExamRequest(ExamRequest $examRequest)
    {
        $patient = auth()->user()->patient;

        if ($examRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if (in_array($examRequest->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Impossible d\'annuler cette demande.');
        }

        $examRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Demande d\'examen annulée.');
    }

    /**
     * TIER 1.5 — Patient Health Trends page
     */
    public function healthTrends()
    {
        $patient = auth()->user()->patient;
        if (!$patient) return redirect()->route('patient.dashboard');

        return view('patient.health-trends', [
            'user' => auth()->user(),
            'patient' => $patient,
        ]);
    }

    public function healthTrendsData()
    {
        $patient = auth()->user()->patient;
        if (!$patient) return response()->json(['success' => false], 403);

        $service = new \App\Services\PatientHealthTrendsService($patient);

        return response()->json([
            'success' => true,
            'trends' => $service->getTrends(),
            'summary' => $service->getSummary(),
        ]);
    }

    /**
     * TIER 2.2 — Chat with doctor
     */
    public function chat(\App\Models\Doctor $doctor)
    {
        $patient = auth()->user()->patient;
        if (!$patient) return redirect()->route('patient.dashboard');

        $access = \App\Models\DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('access_status', 'granted')
            ->first();

        if (!$access) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $userId = auth()->id();
        \App\Models\ChatMessage::where('sender_id', $doctor->user_id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('patient.chat', [
            'doctor' => $doctor,
            'user' => auth()->user(),
        ]);
    }

    public function chatMessages(\App\Models\Doctor $doctor)
    {
        $patient = auth()->user()->patient;
        $access = \App\Models\DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('access_status', 'granted')
            ->first();

        if (!$access) return response()->json(['success' => false], 403);

        $userId = auth()->id();
        $messages = \App\Models\ChatMessage::where(function ($q) use ($userId, $doctor) {
            $q->where('sender_id', $userId)->where('receiver_id', $doctor->user_id);
        })->orWhere(function ($q) use ($userId, $doctor) {
            $q->where('sender_id', $doctor->user_id)->where('receiver_id', $userId);
        })
        ->with('sender')
        ->latest()
        ->limit(100)
        ->get()
        ->reverse()
        ->values();

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function chatSend(\App\Models\Doctor $doctor, Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $patient = auth()->user()->patient;
        $access = \App\Models\DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('access_status', 'granted')
            ->first();

        if (!$access) return response()->json(['success' => false], 403);

        $msg = \App\Models\ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $doctor->user_id,
            'message' => $request->message,
        ]);

        \App\Services\NotificationService::send(
            $doctor->user_id,
            'Nouveau message de ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
            $request->message,
            'general'
        );

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function chatUnreadCount()
    {
        $userId = auth()->id();
        $count = \App\Models\ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['success' => true, 'unread_count' => $count]);
    }

    /**
     * TIER 2.4 — Multi-Lab Splitting suggestions
     */
    public function splitSuggestions(ExamRequest $examRequest)
    {
        $patient = auth()->user()->patient;
        if (!$patient || $examRequest->patient_id !== $patient->id) abort(403);

        $service = new \App\Services\MultiLabSplittingService($examRequest);
        $suggestions = $service->getSplitSuggestions();

        return response()->json([
            'success' => true,
            'split' => $suggestions,
        ]);
    }

    public function applySplit(ExamRequest $examRequest, Request $request)
    {
        $patient = auth()->user()->patient;
        if (!$patient || $examRequest->patient_id !== $patient->id) abort(403);

        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.labo_id' => 'required|exists:labos,id',
            'assignments.*.exam_ids' => 'required|array|min:1',
            'assignments.*.exam_ids.*' => 'exists:exams,id',
        ]);

        $service = new \App\Services\MultiLabSplittingService($examRequest);
        $success = $service->assignSplit($request->assignments);

        if ($success) {
            return redirect()->route('patient.dashboard')
                ->with('success', 'Prescription répartie entre les laboratoires avec succès.');
        }

        return back()->with('error', 'Erreur lors de la répartition.');
    }

    public function scanDoctor(string $code)
    {
        $doctor = \App\Models\Doctor::where('doctor_code', $code)->first();
        if (!$doctor) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Code médecin invalide. Veuillez vérifier le QR code scanné.');
        }

        $patient = auth()->user()->patient;
        if (!$patient) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Impossible de vous identifier en tant que patient.');
        }

        $existing = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->first();

        if ($existing) {
            if ($existing->access_status === 'blocked') {
                return redirect()->route('patient.dashboard')
                    ->with('error', 'Ce médecin a été bloqué. Vous devez le débloquer depuis votre espace.');
            }
            if ($existing->access_status === 'granted') {
                return redirect()->route('patient.dashboard')
                    ->with('info', 'Vous êtes déjà lié(e) à ce médecin.');
            }
            $existing->update([
                'access_status' => 'granted',
                'expires_at' => now()->addMonths(6),
            ]);
        } else {
            DoctorPatientAccess::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'access_status' => 'granted',
                'expires_at' => now()->addMonths(6),
            ]);
        }

        $doctorName = $doctor->user->first_name . ' ' . $doctor->user->last_name;
        $patientName = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        \App\Models\Notification::create([
            'user_id' => $doctor->user_id,
            'title' => 'Nouveau patient lié',
            'message' => "{$patientName} a scanné votre QR code et vous est désormais lié(e) en tant que patient. Vous avez accès à son dossier médical.",
            'notification_type' => 'access_request',
            'reference_id' => $existing?->id ?? DoctorPatientAccess::latest()->first()->id,
        ]);

        return redirect()->route('patient.dashboard')
            ->with('success', "Vous êtes maintenant lié(e) au Dr. {$doctorName}. Votre dossier est partagé avec lui.");
    }
}
