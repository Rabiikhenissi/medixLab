<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Show the pending two-factor challenge for a login in progress.
     */
    public function loginChallenge(): View|RedirectResponse
    {
        $pending = session()->get('two_factor');

        if (! is_array($pending) || empty($pending['user_id']) || ! User::find($pending['user_id'])) {
            return redirect()->route('home');
        }

        return view('auth.two-factor-login');
    }

    /**
     * Verify the code entered for a pending login and complete the authentication.
     */
    public function loginVerify(Request $request): RedirectResponse
    {
        $pending = session()->get('two_factor');

        if (! is_array($pending) || empty($pending['user_id'])) {
            return redirect()->route('home');
        }

        $user = User::find($pending['user_id']);

        if (! $user || ! $user->twoFactorEnabled()) {
            session()->forget('two_factor');

            return redirect()->route('home');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:8',
        ]);

        if (! $this->twoFactor->verify($user->two_factor_secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Le code saisi est invalide ou a expiré.']);
        }

        Auth::login($user, (bool) ($pending['remember'] ?? false));
        $request->session()->regenerate();
        $intended = $pending['intended'] ?? route('home');
        session()->forget('two_factor');

        $user->update(['last_login_at' => now()]);

        return redirect($intended);
    }

    /**
     * Show the two-factor management screen for the authenticated user.
     */
    public function showSetup(Request $request): View
    {
        $user = Auth::user();

        if (! $user->twoFactorEnabled()) {
            $secret = $request->session()->get('two_factor.setup.secret');

            if (! $secret) {
                $secret = $this->twoFactor->generateSecret();
                $request->session()->put('two_factor.setup.secret', $secret);
            }

            return view('profile.two-factor', [
                'secret' => $secret,
                'qrCode' => $this->twoFactor->qrCodeSvg($user->email, $secret),
                'otpauthUrl' => $this->twoFactor->otpauthUrl($user->email, $secret),
            ]);
        }

        return view('profile.two-factor', [
            'secret' => null,
            'qrCode' => null,
            'otpauthUrl' => null,
        ]);
    }

    /**
     * Confirm the pending setup secret after the user scanned it and entered a code.
     */
    public function enable(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $secret = $request->session()->get('two_factor.setup.secret');

        if (! $secret || $user->twoFactorEnabled()) {
            return back()->withErrors(['code' => 'Aucune activation en cours.']);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:8',
        ]);

        if (! $this->twoFactor->verify($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Le code saisi est invalide.']);
        }

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);
        $request->session()->forget('two_factor.setup.secret');

        return back()->with('success', 'Authentification à deux facteurs activée.');
    }

    /**
     * Disable two-factor authentication after confirming the account password.
     */
    public function disable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $request->session()->forget('two_factor.setup.secret');

        return back()->with('success', 'Authentification à deux facteurs désactivée.');
    }
}
