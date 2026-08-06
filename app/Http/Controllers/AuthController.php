<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use App\Models\Doctor;
use App\Models\Group;
use App\Models\Labo;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Services\CodeGeneratorService;
use App\Services\TwoFactorService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Handle login requests for a given role.
     *
     * @return RedirectResponse
     */
    public function login(Request $request, string $role)
    {
        // validate login credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // remember the session if requested
        $remember = $request->filled('remember');

        // attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            // protect against session fixation
            $request->session()->regenerate();
            $user = Auth::user();

            // Two-factor challenge: do not authenticate yet, email a one-time
            // code, stash the pending login in the session and ask for the code.
            // A previously trusted device skips the challenge entirely.
            if ($user->twoFactorEnabled()
                && ! $this->twoFactor->isTrustedDevice($user, $request->cookie(TwoFactorService::TRUST_COOKIE_NAME))) {
                $intended = $user->admin
                    ? $this->safeIntended($request, 'admin.dashboard')
                    : $this->safeIntended($request, $role.'.dashboard');

                $request->session()->put('two_factor', [
                    'user_id' => $user->id,
                    'remember' => $remember,
                    'intended' => $intended,
                ]);
                Auth::logout();

                try {
                    $this->twoFactor->sendCode($user->fresh());
                } catch (\Throwable $e) {
                    \Log::error('2FA email could not be sent: '.$e->getMessage());
                }

                return redirect()->route('two-factor.login');
            }

            // Check if user is an admin
            if ($user->admin) {
                return redirect($this->safeIntended($request, 'admin.dashboard'));
            }

            // Validate that the user matches the role they are logging into
            if ($role === 'doctor' && ! $user->doctor) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que médecin.',
                ])->withInput($request->only('email', 'remember'));
            }

            if ($role === 'patient' && ! $user->patient) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que patient.',
                ])->withInput($request->only('email', 'remember'));
            }

            if ($role === 'center' && ! $user->staff) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que centre médical.',
                ])->withInput($request->only('email', 'remember'));
            }

            // Track last login time
            $user->update(['last_login_at' => now()]);

            // redirect to the dashboard of the user's role
            return redirect($this->safeIntended($request, $role.'.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Return the intended URL only if it is a real page of the user's role,
     * otherwise fall back to the dashboard route. This prevents landing on
     * JSON/AJAX endpoints (e.g. /notifications/unread-count) after login.
     */
    private function safeIntended(Request $request, string $fallbackRoute): string
    {
        $intended = $request->session()->pull('url.intended');

        if (is_string($intended) && $intended !== '') {
            try {
                $route = app('router')->getRoutes()->match(Request::create($intended));
                $name = $route ? $route->getName() : null;

                $isAjax = str_contains((string) $name, '.api.')
                    || str_contains((string) $name, '.get-')
                    || preg_match('/(.*-count$|.*-requests$|mark-as-read|mark-all-read|respond-access|block-doctor|unblock-doctor|exam-details|.*-lookup$|.*-search$)/', (string) $name);

                $role = explode('.', $fallbackRoute)[0];

                if ($name && ! $isAjax && str_starts_with($name, $role.'.')) {
                    return $intended;
                }
            } catch (\Throwable $e) {
                // fall through to dashboard
            }
        }

        return route($fallbackRoute);
    }

    /**
     * Handle registration requests for a given role.
     *
     * @return RedirectResponse
     */
    public function register(Request $request, string $role)
    {
        // register a doctor account
        if ($role === 'doctor') {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'specialty' => 'required|string|max:255',
                'address' => 'nullable|string',
                'password' => 'required|string|min:8|confirmed',
                'accept_terms' => 'required|accepted',
                'accept_privacy' => 'required|accepted',
            ]);

            // run doctor creation in a database transaction
            $user = DB::transaction(function () use ($data) {
                $group = Group::firstOrCreate(['code' => 'doctor'], ['name' => 'Doctor']);

                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'password' => Hash::make($data['password']),
                    'group_id' => $group->id,
                ]);

                // Create doctor with temporary unique code
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'speciality' => $data['specialty'],
                    'doctor_code' => 'TEMP-'.Str::random(10),
                ]);

                // Generate real unique code using database ID and update
                $realCode = CodeGeneratorService::generateDoctorCode($doctor->id, $data['specialty']);
                $doctor->update(['doctor_code' => $realCode]);

                return $user;
            });

            Auth::login($user);
            $user->sendEmailVerificationNotification();
            $this->recordConsents($user);

            return redirect()->route('doctor.dashboard');
        }

        // register a patient account
        if ($role === 'patient') {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'country' => 'required|string|max:255',
                'state_code' => 'required|string|max:255',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:M,F',
                'password' => 'required|string|min:8|confirmed',
                'accept_terms' => 'required|accepted',
                'accept_privacy' => 'required|accepted',
            ]);

            // run patient creation in a database transaction
            $user = DB::transaction(function () use ($data) {
                $group = Group::firstOrCreate(['code' => 'patient'], ['name' => 'Patient']);

                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'password' => Hash::make($data['password']),
                    'group_id' => $group->id,
                ]);

                // Create patient with temporary unique code
                $patient = Patient::create([
                    'user_id' => $user->id,
                    'patient_code' => 'TEMP-'.Str::random(10),
                    'blood_group' => $data['blood_group'] ?? null,
                    'date_of_birth' => $data['birth_date'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'country' => $data['country'],
                    'state_code' => $data['state_code'],
                ]);

                // Generate real unique code using database ID and country
                $realCode = CodeGeneratorService::generatePatientCode($patient->id, $data['country']);
                $patient->update(['patient_code' => $realCode]);

                return $user;
            });

            Auth::login($user);
            $user->sendEmailVerificationNotification();
            $this->recordConsents($user);

            return redirect()->route('patient.dashboard');
        }

        // register a medical center account
        if ($role === 'center') {
            $data = $request->validate([
                'center_name' => 'required|string|max:255',
                'responsible' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'password' => 'required|string|min:8|confirmed',
                'accept_terms' => 'required|accepted',
                'accept_privacy' => 'required|accepted',
            ]);

            // run center creation in a database transaction
            $user = DB::transaction(function () use ($data) {
                $countries = [
                    'TN' => 'Tunisie',
                    'FR' => 'France',
                    'MA' => 'Maroc',
                    'DZ' => 'Algérie',
                    'autre' => 'Autre',
                ];

                // create the laboratory for the center
                $labo = Labo::create([
                    'name' => $data['center_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'city' => $data['city'],
                    'country' => $countries[$data['country'] ?? ''] ?? ($data['country'] ?? null),
                    'address' => $data['address'] ?? null,
                ]);

                // split the responsible name into first and last name
                $names = explode(' ', trim($data['responsible']), 2);
                $firstName = $names[0];
                $lastName = $names[1] ?? 'Responsable';

                $group = Group::firstOrCreate(['code' => 'center'], ['name' => 'Medical Center']);

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'password' => Hash::make($data['password']),
                    'group_id' => $group->id,
                ]);

                // Create staff with temporary unique code
                $staff = Staff::create([
                    'user_id' => $user->id,
                    'laboratory_id' => $labo->id,
                    'staff_code' => 'TEMP-'.Str::random(10),
                ]);

                // Generate real unique code using staff ID and lab name
                $realCode = CodeGeneratorService::generateStaffCode($staff->id, $labo->name);
                $staff->update(['staff_code' => $realCode]);

                return $user;
            });

            Auth::login($user);
            $user->sendEmailVerificationNotification();
            $this->recordConsents($user);

            return redirect()->route('center.dashboard');
        }

        abort(404);
    }

    /**
     * Handle logout requests: sign out, invalidate the session and redirect.
     *
     * @return RedirectResponse
     */
    public function logout(Request $request, string $role)
    {
        Auth::logout();

        // clear the session and regenerate the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // admins go back to the home page, others to their login page
        if ($role === 'admin') {
            return redirect()->route('home');
        }

        return redirect()->route($role.'.login');
    }

    /**
     * Show the forgot-password form for a given role.
     */
    public function showForgotPassword(string $role)
    {
        return view('auth.forgot-password', compact('role'));
    }

    /**
     * Send the password reset link email.
     *
     * @return RedirectResponse
     */
    public function sendResetLink(Request $request, string $role)
    {
        $request->validate(['email' => 'required|email']);

        // dispatch the reset link and report the broker status
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }

    /**
     * Show the password reset form (with token from email link).
     *
     * @return View
     */
    public function showResetPassword(Request $request, string $token, string $role)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'role' => $role,
        ]);
    }

    /**
     * Handle the new password submission.
     *
     * @return RedirectResponse
     */
    public function resetPassword(Request $request, string $role)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // reset the password through the broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route("{$role}.login")
                ->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }

    /**
     * Log the registration consent given for the current versions of the
     * legal documents, alongside the technical context (IP + user agent).
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
