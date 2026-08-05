<?php

namespace App\Http\Controllers;

use App\Mail\InviteMail;
use App\Models\Admin;
use App\Models\Consent;
use App\Models\Doctor;
use App\Models\Group;
use App\Models\Invite;
use App\Models\Labo;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InviteController extends Controller
{
    /**
     * Show the form to email a new account invitation.
     *
     * @return View
     */
    public function create()
    {
        $groups = Group::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();
        $laboratories = Labo::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();

        return view('admin.users.invite', compact('groups', 'laboratories'));
    }

    /**
     * Store a new pending invitation and email the invite link.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $centerGroup = Group::where('code', 'center')->first();
        $centerGroupId = $centerGroup ? $centerGroup->id : null;

        $data = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'group_id' => 'required|exists:groups,id',
            'laboratory_id' => ($centerGroupId ? 'required_if:group_id,'.$centerGroupId : 'nullable').'|nullable|exists:labos,id',
        ]);

        if (Invite::where('email', $data['email'])->where('status', Invite::STATUS_PENDING)->exists()) {
            return back()->withErrors([
                'email' => 'Une invitation en attente existe déjà pour cette adresse email.',
            ])->withInput();
        }

        $invite = Invite::create([
            'email' => $data['email'],
            'token' => Str::random(64),
            'group_id' => $data['group_id'],
            'laboratory_id' => $data['laboratory_id'] ?? null,
            'invited_by' => auth()->id(),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'status' => Invite::STATUS_PENDING,
            'expires_at' => now()->addDays((int) config('legal.invite_days', 7)),
        ]);

        Mail::to($invite->email)->send(new InviteMail($invite));

        return redirect()->route('admin.users.index')->with('success', 'Invitation envoyée à '.$invite->email.'.');
    }

    /**
     * Show the account-activation form linked from the invitation email.
     *
     * @return View|RedirectResponse
     */
    public function accept(string $token)
    {
        $invite = Invite::with('group')->where('token', $token)->first();

        if (! $invite || ! $invite->isUsable()) {
            return redirect()->route('home')->with('error', 'Cette invitation est invalide ou a expiré.');
        }

        return view('auth.invite-accept', ['invite' => $invite]);
    }

    /**
     * Create the account for the invited person and mark the invite accepted.
     *
     * @return RedirectResponse
     */
    public function acceptStore(Request $request, string $token)
    {
        $invite = Invite::with('group')->where('token', $token)->first();

        if (! $invite || ! $invite->isUsable()) {
            return redirect()->route('home')->with('error', 'Cette invitation est invalide ou a expiré.');
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'accept_terms' => 'required|accepted',
            'accept_privacy' => 'required|accepted',
        ]);

        $user = DB::transaction(function () use ($invite, $data) {
            $group = $invite->group;

            // The email is trusted: the person clicked the link sent to it.
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $invite->email,
                'password' => Hash::make($data['password']),
                'group_id' => $group->id,
                'email_verified_at' => now(),
            ]);

            if ($group->code === 'admin') {
                Admin::create(['user_id' => $user->id]);
            } elseif ($group->code === 'doctor') {
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'speciality' => 'Généraliste',
                    'doctor_code' => 'TEMP-'.Str::random(10),
                ]);
                $doctor->update(['doctor_code' => CodeGeneratorService::generateDoctorCode($doctor->id, 'Généraliste')]);
            } elseif ($group->code === 'patient') {
                $patient = Patient::create([
                    'user_id' => $user->id,
                    'patient_code' => 'TEMP-'.Str::random(10),
                    'country' => 'TN',
                ]);
                $patient->update(['patient_code' => CodeGeneratorService::generatePatientCode($patient->id, 'TN')]);
            } elseif ($group->code === 'center') {
                $staff = Staff::create([
                    'user_id' => $user->id,
                    'laboratory_id' => $invite->laboratory_id,
                    'staff_code' => 'TEMP-'.Str::random(10),
                ]);
                $labo = $staff->laboratory;
                $staff->update(['staff_code' => CodeGeneratorService::generateStaffCode($staff->id, $labo?->name ?? 'Labo')]);
            }

            $this->recordConsents($user);

            $invite->update([
                'status' => Invite::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $dashboard = match ($user->group->code ?? '') {
            'admin' => 'admin.dashboard',
            'doctor' => 'doctor.dashboard',
            'patient' => 'patient.dashboard',
            'center' => 'center.dashboard',
            default => 'home',
        };

        return redirect()->route($dashboard);
    }

    /**
     * Log the consents given while activating the invited account.
     */
    private function recordConsents(User $user): void
    {
        $now = now();

        foreach ([
            [Consent::TYPE_TERMS, config('legal.terms_version')],
            [Consent::TYPE_PRIVACY, config('legal.privacy_version')],
        ] as [$type, $version]) {
            Consent::create([
                'user_id' => $user->id,
                'consent_type' => $type,
                'version' => $version,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'accepted_at' => $now,
            ]);
        }
    }
}
