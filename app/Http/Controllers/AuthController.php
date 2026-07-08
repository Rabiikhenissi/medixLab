<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Labo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle login requests.
     */
    public function login(Request $request, string $role)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Validate that the user matches the role they are logging into
            if ($role === 'doctor' && !$user->doctor) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que médecin.',
                ])->withInput($request->only('email', 'remember'));
            }

            if ($role === 'patient' && !$user->patient) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que patient.',
                ])->withInput($request->only('email', 'remember'));
            }

            if ($role === 'center' && !$user->staff) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Cet email n\'est pas enregistré en tant que centre médical.',
                ])->withInput($request->only('email', 'remember'));
            }

            return redirect()->intended(route($role . '.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Handle registration requests.
     */
    public function register(Request $request, string $role)
    {
        if ($role === 'doctor') {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'cnom_id' => 'required|string|max:255|unique:doctors,doctor_code',
                'specialty' => 'required|string|max:255',
                'address' => 'nullable|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

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

            Doctor::create([
                'user_id' => $user->id,
                'speciality' => $data['specialty'],
                'doctor_code' => $data['cnom_id'],
            ]);

            Auth::login($user);
            return redirect()->route('doctor.dashboard');
        }

        if ($role === 'patient') {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

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

            Patient::create([
                'user_id' => $user->id,
                'patient_code' => 'PAT-' . strtoupper(Str::random(8)),
            ]);

            Auth::login($user);
            return redirect()->route('patient.dashboard');
        }

        if ($role === 'center') {
            $data = $request->validate([
                'center_name' => 'required|string|max:255',
                'responsible' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'address' => 'nullable|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $labo = Labo::create([
                'name' => $data['center_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'address' => $data['address'] ?? null,
            ]);

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

            Staff::create([
                'user_id' => $user->id,
                'laboratory_id' => $labo->id,
                'staff_code' => 'STF-' . strtoupper(Str::random(8)),
            ]);

            Auth::login($user);
            return redirect()->route('center.dashboard');
        }

        abort(404);
    }

    /**
     * Handle logout requests.
     */
    public function logout(Request $request, string $role)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($role . '.login');
    }
}
