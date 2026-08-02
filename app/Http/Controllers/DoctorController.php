<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExamRequestFormRequest;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\DoctorPatientAccess;
use App\Models\Exam;
use App\Models\ExamGroup;
use App\Models\ExamGroupItem;
use App\Models\ExamRequest;
use App\Models\Notification;
use App\Models\Patient;
use App\Services\ExamRequestService;
use App\Services\ExamSuggestionService;
use App\Services\NotificationService;
use App\Services\PatientHealthTrendsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    /**
     * Get all notifications for doctor
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = Notification::forUser($user->id)
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
                    'type' => $notification->notification_type ?? 'general',
                    'reference_id' => $notification->reference_id,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Notification::forUser($user->id)->unread()->count();

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
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->where('is_archive', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show patient search page
     */
    public function patientSearch()
    {
        return view('doctor.patient-search');
    }

    /**
     * Search patient by code
     */
    public function searchPatient(Request $request)
    {
        $request->validate([
            'patient_code' => 'required|string',
        ]);

        // Find the patient by their unique code
        $patient = Patient::where('patient_code', $request->patient_code)
            ->with('user')
            ->first();

        if (! $patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient non trouvé.',
            ], 404);
        }

        $doctor = Auth::user()->doctor;
        // Check the doctor's access record for this patient
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->first();

        if ($access && $access->access_status === 'blocked') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas accéder à ce patient.',
            ], 403);
        }

        $hasGrantedAccess = $access && $access->access_status === 'granted' && ! $access->isExpired();

        // Build the safe patient payload
        $patientData = [
            'id' => $patient->id,
            'patient_code' => $patient->patient_code,
            'user' => [
                'first_name' => $patient->user->first_name,
                'last_name' => $patient->user->last_name,
            ],
        ];

        // Full PII (DOB, email, phone) only revealed once access is granted
        if ($hasGrantedAccess) {
            $patientData['date_of_birth'] = $patient->date_of_birth;
            $patientData['user']['email'] = $patient->user->email;
            $patientData['user']['phone'] = $patient->user->phone;
        }

        return response()->json([
            'success' => true,
            'patient' => $patientData,
            'access_status' => $access ? $access->access_status : 'none',
        ]);
    }

    /**
     * Request access to patient
     */
    public function requestAccess(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($request->patient_id);

        // Check if access already exists
        $existingAccess = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->first();

        if ($existingAccess) {
            if ($existingAccess->access_status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas demander l\'accès à ce patient.',
                ], 403);
            }

            if ($existingAccess->access_status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Une demande d\'accès est déjà en attente.',
                ], 409);
            }

            if ($existingAccess->access_status === 'granted') {
                return response()->json([
                    'success' => true,
                    'message' => 'Accès déjà autorisé.',
                    'access_granted' => true,
                    'patient_id' => $patient->id,
                ]);
            }
        }

        // Create new access request
        $access = DoctorPatientAccess::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'access_status' => 'pending',
        ]);

        // Create notification for patient
        Notification::create([
            'user_id' => $patient->user_id,
            'title' => 'Demande d\'accès médicale',
            'message' => 'Dr. '.$doctor->user->first_name.' '.$doctor->user->last_name.' ('.$doctor->speciality.') demande l\'accès à votre dossier médical.',
            'notification_type' => 'access_request',
            'reference_id' => $access->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande d\'accès envoyée. En attente de confirmation du patient.',
            'access_id' => $access->id,
        ]);
    }

    /**
     * Show exams selection page
     */
    public function selectExams(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        // Check if doctor has access to this patient
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return redirect()->route('doctor.patient-search')
                ->with('error', 'Vous n\'avez pas accès à ce patient.');
        }

        $patient->load('user');

        // Load all active exams to choose from
        $exams = Exam::where('is_archive', false)
            ->select('id', 'name', 'code', 'category', 'description', 'preparation_instructions')
            ->get();

        // Load the doctor's saved exam groups
        $examGroups = $doctor->examGroups()
            ->where('is_archive', false)
            ->with('items.exam')
            ->select('id', 'name', 'description')
            ->get();

        return view('doctor.exams-selection', compact('patient', 'exams', 'examGroups'));
    }

    /**
     * Create exam request with selected exams
     */
    public function createExamRequest(CreateExamRequestFormRequest $request)
    {
        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($request->patient_id);

        // Verify access
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce patient.',
            ], 403);
        }

        // Create the exam request via the service
        $examRequest = ExamRequestService::create(
            $doctor,
            $patient,
            $request->exam_ids,
            $request->clinical_notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Demande d\'examens créée avec succès.',
            'exam_request_id' => $examRequest->id,
        ]);
    }

    /**
     * List all exam groups for the current doctor (dedicated page)
     */
    public function examGroupsIndex()
    {
        $doctor = Auth::user()->doctor;
        // Fetch the doctor's active exam groups
        $examGroups = $doctor->examGroups()
            ->where('is_archive', false)
            ->with('items.exam')
            ->latest()
            ->get();

        // Fetch the exams catalog for the picker
        $exams = Exam::where('is_archive', false)
            ->select('id', 'name', 'category')
            ->orderBy('name')
            ->get();

        return view('doctor.exam-groups', [
            'examGroups' => $examGroups,
            'exams' => $exams,
            'editGroup' => null,
        ]);
    }

    /**
     * Store a new exam group (dedicated page form)
     */
    public function examGroupsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'exam_ids' => 'required|array|min:1',
            'exam_ids.*' => 'exists:exams,id',
        ]);

        $doctor = Auth::user()->doctor;

        // Create the new exam group
        $examGroup = ExamGroup::create([
            'doctor_id' => $doctor->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Attach the selected exams to the group
        foreach ($request->exam_ids as $examId) {
            ExamGroupItem::create([
                'exam_group_id' => $examGroup->id,
                'exam_id' => $examId,
            ]);
        }

        return redirect()->route('doctor.exam-groups.index')
            ->with('success', 'Groupe « '.$examGroup->name.' » créé avec succès.');
    }

    /**
     * Show edit form for an exam group
     */
    public function examGroupsEdit(ExamGroup $examGroup)
    {
        $doctor = Auth::user()->doctor;

        if ($examGroup->doctor_id !== $doctor->id) {
            abort(403);
        }

        // Load the exam groups list for the page
        $examGroups = $doctor->examGroups()
            ->where('is_archive', false)
            ->with('items.exam')
            ->latest()
            ->get();

        // Load the exams catalog for the picker
        $exams = Exam::where('is_archive', false)
            ->select('id', 'name', 'category')
            ->orderBy('name')
            ->get();

        $examGroup->load('items.exam');

        return view('doctor.exam-groups', [
            'examGroups' => $examGroups,
            'exams' => $exams,
            'editGroup' => $examGroup,
        ]);
    }

    /**
     * Update an existing exam group (dedicated page)
     */
    public function examGroupsUpdate(Request $request, ExamGroup $examGroup)
    {
        $doctor = Auth::user()->doctor;

        if ($examGroup->doctor_id !== $doctor->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'exam_ids' => 'required|array|min:1',
            'exam_ids.*' => 'exists:exams,id',
        ]);

        // Update the group's basic details
        $examGroup->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Replace items
        ExamGroupItem::where('exam_group_id', $examGroup->id)->delete();
        foreach ($request->exam_ids as $examId) {
            ExamGroupItem::create([
                'exam_group_id' => $examGroup->id,
                'exam_id' => $examId,
            ]);
        }

        return redirect()->route('doctor.exam-groups.index')
            ->with('success', 'Groupe « '.$examGroup->name.' » mis à jour avec succès.');
    }

    /**
     * Hard-delete an exam group
     */
    public function examGroupsDestroy(ExamGroup $examGroup)
    {
        $doctor = Auth::user()->doctor;

        if ($examGroup->doctor_id !== $doctor->id) {
            abort(403);
        }

        $name = $examGroup->name;

        // Delete related items first, then the group
        ExamGroupItem::where('exam_group_id', $examGroup->id)->delete();
        $examGroup->delete();

        return redirect()->route('doctor.exam-groups.index')
            ->with('success', 'Groupe « '.$name.' » supprimé définitivement.');
    }

    /**
     * Apply exam group to patient
     */
    public function applyExamGroup(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'exam_group_id' => 'required|exists:exam_groups,id',
            'clinical_notes' => 'nullable|string|max:500',
        ]);

        $doctor = Auth::user()->doctor;
        // Load the patient and the exam group
        $patient = Patient::findOrFail($request->patient_id);
        $examGroup = ExamGroup::with('exams')->findOrFail($request->exam_group_id);

        // Reject groups that do not belong to the doctor
        if ($examGroup->doctor_id !== $doctor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce groupe d\'examens ne vous appartient pas.',
            ], 403);
        }

        // Reject groups with no exams
        if ($examGroup->exams->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce groupe ne contient aucun examen.',
            ], 422);
        }

        // Verify the doctor has active access to the patient
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('access_status', 'granted')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $access) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce patient.',
            ], 403);
        }

        $examCount = $examGroup->exams->count();

        // Create the exam request in a single transaction
        $examRequest = DB::transaction(function () use ($doctor, $patient, $examGroup, $request) {
            // Create the exam request record
            $examRequest = ExamRequest::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'status' => 'pending',
                'clinical_notes' => $request->clinical_notes,
            ]);

            // Attach each exam from the group
            foreach ($examGroup->exams as $exam) {
                ExamRequestItem::create([
                    'exam_request_id' => $examRequest->id,
                    'exam_id' => $exam->id,
                ]);
            }

            // Notify the patient of the new prescription
            Notification::create([
                'user_id' => $patient->user_id,
                'title' => 'Nouvelle demande d\'analyses',
                'message' => 'Dr. '.$doctor->user->first_name.' '.$doctor->user->last_name.' vous a prescrit le groupe « '.$examGroup->name.' » ('.$examCount.' examen(s)).',
                'notification_type' => 'exam_request',
                'reference_id' => $examRequest->id,
            ]);

            return $examRequest;
        });

        return response()->json([
            'success' => true,
            'message' => 'Groupe d\'examens appliqué avec succès.',
            'exam_request_id' => $examRequest->id,
        ]);
    }

    /**
     * Show create form for a new exam group (dedicated page)
     */
    public function examGroupsCreate()
    {
        $exams = Exam::where('is_archive', false)
            ->select('id', 'name', 'category')
            ->orderBy('name')
            ->get();

        return view('doctor.exam-groups-create', compact('exams'));
    }

    /**
     * Store a new exam group via API (JSON) — used from exams-selection page
     */
    public function storeExamGroupApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'exam_ids' => 'required|array|min:1',
            'exam_ids.*' => 'exists:exams,id',
        ]);

        $doctor = Auth::user()->doctor;

        // Create the new exam group
        $examGroup = ExamGroup::create([
            'doctor_id' => $doctor->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Attach the selected exams to the group
        foreach ($request->exam_ids as $examId) {
            ExamGroupItem::create([
                'exam_group_id' => $examGroup->id,
                'exam_id' => $examId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Groupe « '.$examGroup->name.' » créé avec succès.',
            'group' => [
                'id' => $examGroup->id,
                'name' => $examGroup->name,
                'description' => $examGroup->description,
                'exam_count' => count($request->exam_ids),
            ],
        ]);
    }

    /**
     * Submit doctor's interpretation for a completed exam request
     */
    public function submitInterpretation(Request $request, ExamRequest $examRequest)
    {
        $doctor = auth()->user()->doctor;
        if (! $doctor || $examRequest->doctor_id !== $doctor->id) {
            abort(403, 'Action non autorisée.');
        }

        if ($examRequest->status !== 'completed') {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez pas interpréter un examen non terminé par le laboratoire.');
        }

        $request->validate([
            'doctor_interpretation' => 'required|string',
        ]);

        // Store the interpretation and mark the results as approved
        $examRequest->update([
            'doctor_interpretation' => $request->doctor_interpretation,
            'approved_by_doctor' => true,
        ]);

        // Create notification for patient
        Notification::create([
            'user_id' => $examRequest->patient->user_id,
            'title' => 'Résultats d\'examens disponibles',
            'message' => 'Dr. '.$doctor->user->first_name.' '.$doctor->user->last_name.' a validé et interprété vos résultats d\'analyses.',
            'notification_type' => 'exam_request',
            'reference_id' => $examRequest->id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Interprétation soumise et résultats validés avec succès.');
    }

    /**
     * Doctor's "My Patients" — all patients with active access + recent history
     */
    public function myPatients()
    {
        $doctor = auth()->user()->doctor;

        // Fetch active accesses with patient data and recent requests
        $accesses = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->active()
            ->with(['patient.user', 'patient.examRequests' => function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id)->latest()->limit(5);
            }])
            ->latest('updated_at')
            ->get();

        return view('doctor.my-patients', compact('accesses'));
    }

    /**
     * Print / PDF export of a completed exam request (Task 3.2)
     */
    public function printExamRequest(ExamRequest $examRequest)
    {
        $doctor = auth()->user()->doctor;

        if (! $doctor || $examRequest->doctor_id !== $doctor->id) {
            abort(403);
        }

        $examRequest->load(['doctor.user', 'patient.user', 'laboratory', 'items.exam', 'items.resultLabo.details']);

        return view('patient.print-exam-request', compact('examRequest'));
    }

    /**
     * Cancel an exam request (only if not completed)
     */
    public function cancelExamRequest(ExamRequest $examRequest)
    {
        $doctor = auth()->user()->doctor;

        if ($examRequest->doctor_id !== $doctor->id) {
            abort(403);
        }

        if (in_array($examRequest->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Impossible d\'annuler cette demande.');
        }

        $examRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Demande d\'examen annulée.');
    }

    /**
     * TIER 1.4 — Smart Exam Suggestions (AJAX API)
     */
    public function smartSuggestions(Patient $patient, Request $request)
    {
        $doctor = Auth::user()->doctor;

        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        // Normalize the already selected exam ids
        $alreadySelected = $request->input('exam_ids', []);
        if (! is_array($alreadySelected)) {
            $alreadySelected = array_filter(explode(',', $alreadySelected));
        }
        $alreadySelected = array_map('intval', $alreadySelected);

        // Compute suggestions via the service
        $service = new ExamSuggestionService($patient);
        $suggestions = $service->getSuggestions($alreadySelected);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * TIER 1.4 — Patient Health Trends (doctor view, AJAX)
     */
    public function patientHealthTrends(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        // Build the trends via the service
        $service = new PatientHealthTrendsService($patient);

        return response()->json([
            'success' => true,
            'trends' => $service->getTrends(),
            'summary' => $service->getSummary(),
        ]);
    }

    /**
     * TIER 2.2 — Chat with patient
     */
    public function chat(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $user = Auth::user();

        // Mark incoming messages from the patient as read
        ChatMessage::where('sender_id', $patient->user_id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $patient->load('user');

        return view('doctor.chat', [
            'patient' => $patient,
            'user' => $user,
        ]);
    }

    /**
     * Return the chat history with a patient as JSON
     */
    public function chatMessages(Patient $patient)
    {
        $doctor = Auth::user()->doctor;
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return response()->json(['success' => false], 403);
        }

        $userId = Auth::id();
        // Fetch messages exchanged in both directions with the patient
        $messages = ChatMessage::where(function ($q) use ($userId, $patient) {
            $q->where('sender_id', $userId)->where('receiver_id', $patient->user_id);
        })->orWhere(function ($q) use ($userId, $patient) {
            $q->where('sender_id', $patient->user_id)->where('receiver_id', $userId);
        })
            ->with('sender')
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    /**
     * Send a chat message to a patient
     */
    public function chatSend(Patient $patient, Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $doctor = Auth::user()->doctor;
        // Verify the doctor has active access to the patient
        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return response()->json(['success' => false], 403);
        }

        // Store the message
        $msg = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $patient->user_id,
            'message' => $request->message,
        ]);

        // Notify the patient of the new message
        NotificationService::send(
            $patient->user_id,
            'Nouveau message du Dr. '.Auth::user()->first_name,
            $request->message,
            'general'
        );

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Return the count of unread chat messages for the doctor
     */
    public function chatUnreadCount()
    {
        $userId = Auth::id();
        $count = ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['success' => true, 'unread_count' => $count]);
    }

    /**
     * Display the patient's medical records for the doctor
     */
    public function medicalRecords(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        $access = DoctorPatientAccess::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->active()
            ->first();

        if (! $access) {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Vous n\'avez pas accès au dossier de ce patient.');
        }

        // Fetch the patient's exam requests with results
        $examRequests = $patient->examRequests()
            ->where('doctor_id', $doctor->id)
            ->with(['items.exam', 'items.resultLabo.details', 'laboratory'])
            ->latest()
            ->get();

        $patientUser = $patient->user;

        return view('doctor.medical-records', compact('patient', 'patientUser', 'examRequests'));
    }
}
