<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');
        $groupId = $request->input('group_id', '');

        $query = User::with('group');

        if (!$showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

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
     */
    public function create()
    {
        $groups = Group::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();
        $laboratories = \App\Models\Labo::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();

        return view('admin.users.create', [
            'groups' => $groups,
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $centerGroup = Group::where('code', 'center')->first();
        $centerGroupId = $centerGroup ? $centerGroup->id : null;

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
            'laboratory_id' => ($centerGroupId ? 'required_if:group_id,' . $centerGroupId : 'nullable') . '|nullable|exists:labos,id',
        ]);

        DB::transaction(function () use ($data, $request) {
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

            // If the assigned role code is 'admin', also create a record in the admins table
            $group = Group::find($data['group_id']);
            if ($group && $group->code === 'admin') {
                \App\Models\Admin::create([
                    'user_id' => $user->id,
                ]);
            }
            // If the role code is 'doctor', create doctor record
            elseif ($group && $group->code === 'doctor') {
                \App\Models\Doctor::create([
                    'user_id' => $user->id,
                    'doctor_code' => 'DOC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'speciality' => 'Généraliste',
                ]);
            }
            // If patient
            elseif ($group && $group->code === 'patient') {
                \App\Models\Patient::create([
                    'user_id' => $user->id,
                    'patient_code' => 'PAT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                ]);
            }
            // If Medical Center
            elseif ($group && $group->code === 'center') {
                \App\Models\Staff::create([
                    'user_id' => $user->id,
                    'laboratory_id' => $request->input('laboratory_id'),
                    'staff_code' => 'STF-' . strtoupper(\Illuminate\Support\Str::random(8)),
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $groups = Group::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();
        $laboratories = \App\Models\Labo::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name', 'asc')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'groups' => $groups,
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $centerGroup = Group::where('code', 'center')->first();
        $centerGroupId = $centerGroup ? $centerGroup->id : null;

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
            'laboratory_id' => ($centerGroupId ? 'required_if:group_id,' . $centerGroupId : 'nullable') . '|nullable|exists:labos,id',
        ]);

        DB::transaction(function () use ($data, $user, $request) {
            $oldGroup = $user->group;

            $updateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'group_id' => $data['group_id'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Sync with related tables if group changed
            $newGroup = Group::find($data['group_id']);
            if ($oldGroup && $newGroup && $oldGroup->code !== $newGroup->code) {
                // Delete old roles records
                if ($oldGroup->code === 'admin') {
                    \App\Models\Admin::where('user_id', $user->id)->delete();
                } elseif ($oldGroup->code === 'doctor') {
                    \App\Models\Doctor::where('user_id', $user->id)->delete();
                } elseif ($oldGroup->code === 'patient') {
                    \App\Models\Patient::where('user_id', $user->id)->delete();
                } elseif ($oldGroup->code === 'center') {
                    \App\Models\Staff::where('user_id', $user->id)->delete();
                }

                // Create new role record
                if ($newGroup->code === 'admin') {
                    \App\Models\Admin::firstOrCreate(['user_id' => $user->id]);
                } elseif ($newGroup->code === 'doctor') {
                    \App\Models\Doctor::firstOrCreate([
                        'user_id' => $user->id
                    ], [
                        'doctor_code' => 'DOC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                        'speciality' => 'Généraliste',
                    ]);
                } elseif ($newGroup->code === 'patient') {
                    \App\Models\Patient::firstOrCreate([
                        'user_id' => $user->id
                    ], [
                        'patient_code' => 'PAT-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    ]);
                } elseif ($newGroup->code === 'center') {
                    \App\Models\Staff::firstOrCreate([
                        'user_id' => $user->id
                    ], [
                        'laboratory_id' => $request->input('laboratory_id'),
                        'staff_code' => 'STF-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    ]);
                }
            } elseif ($newGroup && $newGroup->code === 'center') {
                // If group is center and hasn't changed, sync the laboratory_id
                \App\Models\Staff::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'laboratory_id' => $request->input('laboratory_id'),
                        'staff_code' => $user->staff ? $user->staff->staff_code : 'STF-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    ]
                );
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Don't let users archive themselves
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas archiver/supprimer votre propre compte.');
        }

        // Toggle archive status
        $user->update(['is_archive' => !$user->is_archive]);

        $message = $user->is_archive
            ? 'Utilisateur archivé avec succès.'
            : 'Utilisateur restauré avec succès.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}
