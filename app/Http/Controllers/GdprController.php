<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\GdprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class GdprController extends Controller
{
    /** List users so an admin can action an export or erasure request. */
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['patient', 'doctor', 'staff'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr', compact('users'));
    }

    /** Stream the personal-data export as a JSON download. */
    public function export(User $user, GdprService $gdpr)
    {
        $data = $gdpr->export($user);

        $this->logGdpr($user, 'export');

        return Response::json($data, 200, [
            'Content-Disposition' => 'attachment; filename=gdpr-export-user-'.$user->id.'.json',
        ]);
    }

    /** Anonymise (or, when requested, hard-erase) the user account. */
    public function erase(Request $request, User $user, GdprService $gdpr): RedirectResponse
    {
        if (! $request->boolean('confirm')) {
            return redirect()
                ->route('admin.gdpr')
                ->with('error', 'Confirmation requise pour lancer l\'effacement.');
        }

        $hard = $request->boolean('hard');
        $gdpr->erase($user, $hard);
        $this->logGdpr($user, $hard ? 'erase-hard' : 'erase');

        return redirect()
            ->route('admin.gdpr')
            ->with('success', $hard
                ? 'Compte #'.$user->id.' entièrement effacé (données cliniques conservées).'
                : 'Compte #'.$user->id.' anonymisé.');
    }

    private function logGdpr(User $user, string $action): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'action' => $action,
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => 'Action RGPD: '.$action.' sur le compte #'.$user->id,
            'changes' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
