<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GroupController extends Controller
{
    protected array $systemGroups = ['admin', 'doctor', 'patient', 'center'];

    /** Drop the cached permission map so sidebar/access checks reflect changes immediately. */
    private function flushGroupPermissionCache(Group $group): void
    {
        Cache::forget('group_permissions_'.$group->id);
    }

    /**
     * Display a listing of groups.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');

        $query = Group::withCount('users');

        // hide archived groups unless explicitly requested
        if (! $showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        // narrow results by search keyword
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
     *
     * @return View
     */
    public function create()
    {
        // load all non-archived features with their actions for the form
        $features = Feature::with(['actions' => function ($q) {
            $q->where('is_archive', false)->orWhereNull('is_archive');
        }])->where('is_archive', false)->orWhereNull('is_archive')->get();

        return view('admin.groups.create', [
            'features' => $features,
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // validate the group fields and its actions
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

        // attach the granted actions to the group
        if (! empty($data['actions'])) {
            $group->actions()->sync($data['actions']);
        }

        $this->flushGroupPermissionCache($group);

        // redirect back to the groups list
        return redirect()->route('admin.groups.index')->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Show the form for editing the specified group.
     *
     * @return View
     */
    public function edit(Group $group)
    {

        // load all non-archived features with their actions for the form
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
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Group $group)
    {

        // validate the group fields and its actions
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:groups,code,'.$group->id,
            'actions' => 'nullable|array',
            'actions.*' => 'exists:actions,id',
        ]);

        $code = Str::slug($data['code']);

        // reject duplicated slugified codes on other groups
        if (Group::where('code', $code)->where('id', '!=', $group->id)->exists()) {
            return back()->withErrors(['code' => 'Ce code de groupe est déjà utilisé.'])->withInput();
        }

        $group->update([
            'name' => $data['name'],
            'code' => $code,
        ]);

        // Sync actions
        $group->actions()->sync($data['actions'] ?? []);

        $this->flushGroupPermissionCache($group);

        return redirect()->route('admin.groups.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Remove the specified group from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(Group $group)
    {

        // Check if group is assigned to active users
        if ($group->users()->exists()) {
            return redirect()->route('admin.groups.index')->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        // Toggle archive status
        $group->update(['is_archive' => ! $group->is_archive]);

        $message = $group->is_archive
            ? 'Rôle archivé avec succès.'
            : 'Rôle restauré avec succès.';

        return redirect()->route('admin.groups.index')->with('success', $message);
    }

    /**
     * Permanently remove the specified group from storage.
     *
     * @return RedirectResponse
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
