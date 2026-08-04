<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Group;
use App\Models\Labo;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @return View
     */
    public function index(Request $request)
    {
        // read the filter inputs from the request
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');
        $groupId = $request->input('group_id', '');

        $query = User::with('group');

        // hide archived users unless explicitly requested
        if (! $showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        // narrow results by search keyword
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // narrow results by group
        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
        $groups = Group::orderBy('name', 'asc')->get();

        return view('admin.users.index', [
            'users' => $users,
            'groups' => $groups,
            'search' => $search,
            'selectedGroup' => $groupId,
            'showArchived' => $showArchived,
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View
     */
    public function create()
    {
        $groups = Group::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();
        $laboratories = Labo::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();

        return view('admin.users.create', [
            'groups' => $groups,
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // validate the user fields
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        // a center-staff group requires a laboratory
        $group = Group::find($data['group_id']);
        if ($group && $group->role_table === 'staff') {
            $data['laboratory_id'] = $request->validate([
                'laboratory_id' => 'required|exists:labos,id',
            ])['laboratory_id'];
        }

        // create the user and its role record in a single transaction
        DB::transaction(function () use ($data, $group) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'group_id' => $data['group_id'],
                'password' => Hash::make($data['password']),
                'is_archive' => false,
            ]);

            // create the matching role record for the group's profile table
            if ($group && $group->role_table === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                ]);
            } elseif ($group && $group->role_table === 'doctor') {
                Doctor::create([
                    'user_id' => $user->id,
                    'doctor_code' => 'DOC-'.strtoupper(Str::random(6)),
                    'speciality' => 'Généraliste',
                ]);
            } elseif ($group && $group->role_table === 'patient') {
                Patient::create([
                    'user_id' => $user->id,
                    'patient_code' => 'PAT-'.strtoupper(Str::random(6)),
                ]);
            } elseif ($group && $group->role_table === 'staff') {
                Staff::create([
                    'user_id' => $user->id,
                    'laboratory_id' => $data['laboratory_id'] ?? null,
                    'staff_code' => 'STF-'.strtoupper(Str::random(8)),
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @return View
     */
    public function edit(User $user)
    {
        $groups = Group::where(function ($q) {
            $q->where('is_archive', false)->orWhereNull('is_archive');
        })->orderBy('name', 'asc');

        // a group can only be changed into another group of the same profile table
        if ($roleTable = $user->group?->role_table) {
            $groups->where('role_table', $roleTable);
        }

        $laboratories = Labo::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'groups' => $groups->get(),
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // validate the updated fields
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        // a group can only be changed into another group of the same profile table
        $newGroup = Group::find($data['group_id']);
        $oldRoleTable = $user->group?->role_table;

        if ($oldRoleTable && $newGroup && $newGroup->role_table !== $oldRoleTable) {
            return back()->withInput()->withErrors([
                'group_id' => 'Le rôle ne peut être modifié que vers un autre groupe de la même table de profil ('
                    .$oldRoleTable
                    .').',
            ]);
        }

        // a center-staff group requires a laboratory
        if ($newGroup && $newGroup->role_table === 'staff') {
            $data['laboratory_id'] = $request->validate([
                'laboratory_id' => 'required|exists:labos,id',
            ])['laboratory_id'];
        }

        DB::transaction(function () use ($data, $user, $newGroup) {
            $oldGroup = $user->group;

            $updateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'group_id' => $data['group_id'],
            ];

            // update only the password when a new one is provided
            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Sync with related tables if profile table changed
            $oldRoleTable = $oldGroup?->role_table;
            $newRoleTable = $newGroup?->role_table;

            if ($oldRoleTable !== $newRoleTable) {
                // Delete old roles records
                if ($oldRoleTable === 'admin') {
                    Admin::where('user_id', $user->id)->delete();
                } elseif ($oldRoleTable === 'doctor') {
                    Doctor::where('user_id', $user->id)->delete();
                } elseif ($oldRoleTable === 'patient') {
                    Patient::where('user_id', $user->id)->delete();
                } elseif ($oldRoleTable === 'staff') {
                    Staff::where('user_id', $user->id)->delete();
                }

                // Create new role record
                if ($newRoleTable === 'admin') {
                    Admin::firstOrCreate(['user_id' => $user->id]);
                } elseif ($newRoleTable === 'doctor') {
                    Doctor::firstOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'doctor_code' => 'DOC-'.strtoupper(Str::random(6)),
                        'speciality' => 'Généraliste',
                    ]);
                } elseif ($newRoleTable === 'patient') {
                    Patient::firstOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'patient_code' => 'PAT-'.strtoupper(Str::random(6)),
                    ]);
                } elseif ($newRoleTable === 'staff') {
                    Staff::firstOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'laboratory_id' => $data['laboratory_id'] ?? null,
                        'staff_code' => 'STF-'.strtoupper(Str::random(8)),
                    ]);
                }
            } elseif ($newRoleTable === 'staff') {
                // If still a staff member, sync the laboratory_id
                Staff::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'laboratory_id' => $data['laboratory_id'] ?? null,
                        'staff_code' => $user->staff ? $user->staff->staff_code : 'STF-'.strtoupper(Str::random(8)),
                    ]
                );
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        // Don't let users archive themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas archiver/supprimer votre propre compte.');
        }

        // Toggle archive status
        $user->update(['is_archive' => ! $user->is_archive]);

        $message = $user->is_archive
            ? 'Utilisateur archivé avec succès.'
            : 'Utilisateur restauré avec succès.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Permanently remove the specified user from storage.
     *
     * @return RedirectResponse
     */
    public function forceDelete(User $user)
    {
        // Don't let users delete themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé définitivement.');
    }

    /**
     * Show the user profile page.
     *
     * @return View
     */
    public function profile()
    {
        $user = auth()->user();

        return view('profile', compact('user'));
    }

    /**
     * Update the user profile.
     *
     * @return RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Specific rules based on role
        if ($user->doctor) {
            $rules['speciality'] = 'required|string|max:255';
        } elseif ($user->patient) {
            $rules['blood_group'] = 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-';
        }

        $data = $request->validate($rules);

        // Update basic info
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update role-specific info
        if ($user->doctor) {
            $user->doctor->update([
                'speciality' => $data['speciality'],
            ]);
        } elseif ($user->patient) {
            $user->patient->update([
                'blood_group' => $data['blood_group'] ?? null,
            ]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
