<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    protected array $systemGroups = ['admin', 'doctor', 'patient', 'center'];

    /**
     * Display a listing of groups.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');

        $query = Group::withCount('users');

        if (!$showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $groups = $query->orderBy('name', 'asc')->paginate(10)->appends($request->query());

        return view('admin.groups.index', [
            'groups' => $groups,
            'search' => $search,
            'showArchived' => $showArchived,
        ]);
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        $features = Feature::with(['actions' => function ($q) {
            $q->where('is_archive', false)->orWhereNull('is_archive');
        }])->where('is_archive', false)->orWhereNull('is_archive')->get();

        return view('admin.groups.create', [
            'features' => $features,
        ]);
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:groups,code',
            'actions' => 'nullable|array',
            'actions.*' => 'exists:actions,id',
        ]);

        $code = Str::slug($data['code']);

        // Check unique again with slugified code
        if (Group::where('code', $code)->exists()) {
            return back()->withErrors(['code' => 'Ce code de groupe est déjà utilisé.'])->withInput();
        }

        $group = Group::create([
            'name' => $data['name'],
            'code' => $code,
            'is_archive' => false,
        ]);

        if (!empty($data['actions'])) {
            $group->actions()->sync($data['actions']);
        }

        return redirect()->route('admin.groups.index')->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group)
    {


        $features = Feature::with(['actions' => function ($q) {
            $q->where('is_archive', false)->orWhereNull('is_archive');
        }])->where('is_archive', false)->orWhereNull('is_archive')->get();

        $groupActionIds = $group->actions->pluck('id')->toArray();

        return view('admin.groups.edit', [
            'group' => $group,
            'features' => $features,
            'groupActionIds' => $groupActionIds,
        ]);
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, Group $group)
    {


        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:groups,code,' . $group->id,
            'actions' => 'nullable|array',
            'actions.*' => 'exists:actions,id',
        ]);

        $code = Str::slug($data['code']);

        if (Group::where('code', $code)->where('id', '!=', $group->id)->exists()) {
            return back()->withErrors(['code' => 'Ce code de groupe est déjà utilisé.'])->withInput();
        }

        $group->update([
            'name' => $data['name'],
            'code' => $code,
        ]);

        // Sync actions
        $group->actions()->sync($data['actions'] ?? []);

        return redirect()->route('admin.groups.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {


        // Check if group is assigned to active users
        if ($group->users()->exists()) {
            return redirect()->route('admin.groups.index')->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        // Toggle archive status
        $group->update(['is_archive' => !$group->is_archive]);

        $message = $group->is_archive
            ? 'Rôle archivé avec succès.'
            : 'Rôle restauré avec succès.';

        return redirect()->route('admin.groups.index')->with('success', $message);
    }

    /**
     * Permanently remove the specified group from storage.
     */
    public function forceDelete(Group $group)
    {
        if ($group->users()->exists()) {
            return redirect()->route('admin.groups.index')->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Rôle supprimé définitivement.');
    }
}
