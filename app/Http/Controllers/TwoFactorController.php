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

        $user = $this->pendingUser($pending);

        if (! $user || ! $user->twoFactorEnabled()) {
            return redirect()->route('home');
        }

        return view('auth.two-factor-login', [
            'email' => $this->maskEmail($user->email),
        ]);
    }

    /**
     * Verify the code entered for a pending login and complete the authentication.
     */
    public function loginVerify(Request $request): RedirectResponse
    {
        $pending = session()->get('two_factor');

        $user = $this->pendingUser($pending);

        if (! $user || ! $user->twoFactorEnabled()) {
            session()->forget('two_factor');

            return redirect()->route('home');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:10',
        ]);

        if (! $this->twoFactor->isCodeValid($user, $validated['code'])) {
            return back()->withErrors(['code' => 'Le code saisi est invalide ou a expiré.']);
        }

        $this->twoFactor->clearCode($user);
        Auth::login($user, (bool) ($pending['remember'] ?? false));
        $request->session()->regenerate();
        $intended = $pending['intended'] ?? route('home');
        session()->forget('two_factor');

        $user->update(['last_login_at' => now()]);

        return redirect($intended);
    }

    /**
     * Resend a fresh code for the pending login.
     */
    public function resendChallenge(Request $request): RedirectResponse
    {
        $user = $this->pendingUser(session()->get('two_factor'));

        if (! $user || ! $user->twoFactorEnabled()) {
            session()->forget('two_factor');

            return redirect()->route('home');
        }

        $this->twoFactor->sendCode($user);

        return back()->with('status', 'Un nouveau code vous a été envoyé par email.');
    }

    /**
     * Show the two-factor management screen for the authenticated user.
     */
    public function showSetup(Request $request): View
    {
        $user = Auth::user();

        if (! $user->twoFactorEnabled()) {
            $request->session()->forget('two_factor.setup.code_sent');

            $hasValidCode = $user->two_factor_code
                && $user->two_factor_code_expires_at
                && $user->two_factor_code_expires_at->isFuture();

            if (! $hasValidCode) {
                $this->twoFactor->sendCode($user);
            }

            $request->session()->put('two_factor.setup.code_sent', true);
        }

        return view('profile.two-factor', [
            'codeSent' => (bool) $request->session()->get('two_factor.setup.code_sent'),
        ]);
    }

    /**
     * Confirm the emailed code and enable two-factor authentication.
     */
    public function enable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->twoFactorEnabled()) {
            return back()->withErrors(['code' => 'La double authentification est déjà activée.']);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:10',
        ]);

        if (! $this->twoFactor->isCodeValid($user, $validated['code'])) {
            return back()->withErrors(['code' => 'Le code saisi est invalide ou a expiré.']);
        }

        $user->update([
            'two_factor_confirmed_at' => now(),
        ]);
        $this->twoFactor->clearCode($user);
        $request->session()->forget('two_factor.setup.code_sent');

        return back()->with('success', 'Authentification à deux facteurs activée. À partir de maintenant, un code vous sera envoyé par email à chaque connexion.');
    }

    /**
     * Resend a fresh code during the enable flow.
     */
    public function resendSetup(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->twoFactorEnabled()) {
            return back()->withErrors(['code' => 'La double authentification est déjà activée.']);
        }

        $this->twoFactor->sendCode($user);
        $request->session()->put('two_factor.setup.code_sent', true);

        return back()->with('status', 'Un nouveau code vous a été envoyé par email.');
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
        $this->twoFactor->clearCode($user);
        $request->session()->forget('two_factor.setup.code_sent');

        return back()->with('success', 'Authentification à deux facteurs désactivée.');
    }

    /** Resolve the pending challenge user from the stored payload, if valid. */
    private function pendingUser(mixed $pending): ?User
    {
        if (! is_array($pending) || empty($pending['user_id'])) {
            return null;
        }

        $user = User::find($pending['user_id']);

        return $user && $user->twoFactorEnabled() ? $user : null;
    }

    /** Show only a hint of the recipient address, e.g. j***@gmail.com. */
    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email), 2, '');

        return mb_substr($local, 0, 1).'***@'.$domain;
    }
}
