<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /** Show the "verify your email" notice to logged-in, unverified users. */
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->homeRedirect($request->user()));
        }

        return view('auth.verify-email');
    }

    /** Mark the account verified via the signed link sent by email. */
    public function verify(Request $request): RedirectResponse
    {
        $user = User::findOrFail((int) $request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect($this->homeRedirect($user))
            ->with('status', 'Votre adresse email a bien été vérifiée.');
    }

    /** Resend the verification email to the logged-in user. */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->homeRedirect($request->user()));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Un nouveau lien de vérification vient d\'être envoyé.');
    }

    /** Role-aware post-login destination for verified users. */
    private function homeRedirect(User $user): string
    {
        if ($user->admin) {
            return route('admin.dashboard');
        }

        if ($user->doctor) {
            return route('doctor.dashboard');
        }

        if ($user->staff) {
            return route('center.dashboard');
        }

        return route('patient.dashboard');
    }
}
