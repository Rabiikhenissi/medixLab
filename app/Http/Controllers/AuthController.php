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

            // Check if user is an admin
            if ($user->admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

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
                'country' => 'required|string|max:10',
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

            $postalCode = $this->extractPostalCode($data['address'] ?? '');
            $countryCodes = [
                'TN' => '216',
                'FR' => '033',
                'MA' => '212',
                'DZ' => '213',
                'autre' => '000'
            ];
            $countryCode = $countryCodes[$data['country']] ?? '000';
            $timestamp = date('YmdHis');
            $patientCode = $countryCode . $postalCode . $timestamp;

            Patient::create([
                'user_id' => $user->id,
                'patient_code' => $patientCode,
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

        if ($role === 'admin') {
            return redirect()->route('home');
        }

        return redirect()->route($role . '.login');
    }

    /**
     * Helper method to extract 3-digit postal code based on the address or city keywords.
     */
    private function extractPostalCode(?string $address): string
    {
        if (empty($address)) {
            return '000';
        }

        // Try to find a 4 or 5 digit number in the address (standard postal codes)
        if (preg_match('/\b(\d{4,5})\b/', $address, $matches)) {
            return str_pad(substr($matches[1], 0, 3), 3, '0', STR_PAD_RIGHT);
        }

        // If no number matches, look for city keywords in Tunisia and map them
        $cityMap = [
            'tunis' => '100',
            'ariana' => '200',
            'ben arous' => '209',
            'manouba' => '201',
            'nabeul' => '800',
            'zaghouan' => '110',
            'bizerte' => '700',
            'beja' => '900',
            'jendouba' => '810',
            'kef' => '710',
            'siliana' => '610',
            'sousse' => '400',
            'monastir' => '500',
            'mahdia' => '510',
            'sfax' => '300',
            'kairouan' => '310',
            'kasserine' => '120',
            'sidi bouzid' => '910',
            'gabes' => '600',
            'medenine' => '410',
            'tataouine' => '320',
            'gafsa' => '210',
            'tozeur' => '220',
            'kebili' => '420'
        ];

        $lowerAddress = mb_strtolower($address);
        foreach ($cityMap as $city => $code) {
            if (mb_strpos($lowerAddress, $city) !== false) {
                return $code;
            }
        }

        return '000';
    }
}
