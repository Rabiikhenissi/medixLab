<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\Heroicons;

class FeatureController extends Controller
{
    /**
     * Display a listing of features.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');

        $query = Feature::withCount(['actions' => function ($q) {
            $q->where('is_archive', false)->orWhereNull('is_archive');
        }]);

        if (!$showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('route_name', 'like', "%{$search}%");
            });
        }

        $features = $query->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.features.index', [
            'features' => $features,
            'search' => $search,
            'showArchived' => $showArchived,
        ]);
    }

    /**
     * Show the form for creating a new feature.
     */
    public function create()
    {
        return view('admin.features.create', [
            'icons' => Heroicons::all()
        ]);
    }

    /**
     * Store a newly created feature in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:features,code',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'is_sidebar' => 'required|boolean',
            'order' => 'required|integer|min:0',
            'view_permission' => 'nullable|string|max:255',
            'actions' => 'nullable|array',
            'actions.*.name' => 'required|string|max:255',
            'actions.*.code' => 'required|string|max:255',
        ]);

        $code = Str::slug($data['code']);

        if (Feature::where('code', $code)->exists()) {
            return back()->withErrors(['code' => 'Ce code de module est déjà utilisé.'])->withInput();
        }

        $actionsData = [];
        if (!empty($request->input('actions'))) {
            $actionCodes = [];
            foreach ($request->input('actions') as $actionInput) {
                if (empty($actionInput['name']) || empty($actionInput['code'])) {
                    continue;
                }
                $actionCode = Str::slug($actionInput['code']);
                if (in_array($actionCode, $actionCodes)) {
                    return back()->withErrors(['actions' => 'Les codes d\'actions soumis doivent être uniques.'])->withInput();
                }
                if (Action::where('code', $actionCode)->exists()) {
                    return back()->withErrors(['actions' => "Le code d'action '{$actionCode}' est déjà utilisé."])->withInput();
                }
                $actionCodes[] = $actionCode;
                $actionsData[] = [
                    'name' => $actionInput['name'],
                    'code' => $actionCode,
                    'is_archive' => false,
                ];
            }
        }

        $feature = Feature::create([
            'name' => $data['name'],
            'code' => $code,
            'route_name' => $data['route_name'] ?: null,
            'icon' => $data['icon'] ?: null,
            'is_sidebar' => (bool)$data['is_sidebar'],
            'order' => (int)$data['order'],
            'view_permission' => $data['view_permission'] ?: null,
            'is_archive' => false,
        ]);

        foreach ($actionsData as $act) {
            $feature->actions()->create($act);
        }

        return redirect()->route('admin.features.index')->with('success', 'Module créé avec succès.');
    }

    /**
     * Show the form for editing the specified feature.
     */
    public function edit(Feature $feature)
    {
        $actions = $feature->actions()
            ->orderBy('is_archive', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.features.edit', [
            'feature' => $feature,
            'actions' => $actions,
            'icons' => Heroicons::all()
        ]);
    }

    /**
     * Update the specified feature in storage.
     */
    public function update(Request $request, Feature $feature)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:features,code,' . $feature->id,
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'is_sidebar' => 'required|boolean',
            'order' => 'required|integer|min:0',
            'view_permission' => 'nullable|string|max:255',
        ]);

        $code = Str::slug($data['code']);

        if (Feature::where('code', $code)->where('id', '!=', $feature->id)->exists()) {
            return back()->withErrors(['code' => 'Ce code de module est déjà utilisé.'])->withInput();
        }

        $feature->update([
            'name' => $data['name'],
            'code' => $code,
            'route_name' => $data['route_name'] ?: null,
            'icon' => $data['icon'] ?: null,
            'is_sidebar' => (bool)$data['is_sidebar'],
            'order' => (int)$data['order'],
            'view_permission' => $data['view_permission'] ?: null,
        ]);

        return redirect()->route('admin.features.index')->with('success', 'Module mis à jour avec succès.');
    }

    /**
     * Remove the specified feature from storage.
     */
    public function destroy(Feature $feature)
    {
        // Toggle archive status
        $feature->update(['is_archive' => !$feature->is_archive]);

        $message = $feature->is_archive
            ? 'Module archivé avec succès.'
            : 'Module restauré avec succès.';

        return redirect()->route('admin.features.index')->with('success', $message);
    }

    /**
     * Permanently remove the specified feature from storage.
     */
    public function forceDelete(Feature $feature)
    {
        $feature->delete();

        return redirect()->route('admin.features.index')->with('success', 'Module supprimé définitivement.');
    }

    /**
     * Store a newly created action under this feature.
     */
    public function storeAction(Request $request, Feature $feature)
    {
        $data = $request->validate([
            'action_name' => 'required|string|max:255',
            'action_code' => 'required|string|max:255|unique:actions,code',
        ]);

        $actionCode = Str::slug($data['action_code']);

        if (Action::where('code', $actionCode)->exists()) {
            return back()->withErrors(['action_code' => 'Ce code d\'action est déjà utilisé.'])->withInput();
        }

        Action::create([
            'feature_id' => $feature->id,
            'name' => $data['action_name'],
            'code' => $actionCode,
            'is_archive' => false,
        ]);

        return back()->with('success', 'Action ajoutée avec succès.');
    }

    /**
     * Update the specified action.
     */
    public function updateAction(Request $request, Action $action)
    {
        $data = $request->validate([
            'action_name' => 'required|string|max:255',
            'action_code' => 'required|string|max:255',
        ]);

        $actionCode = Str::slug($data['action_code']);

        if (Action::where('code', $actionCode)->where('id', '!=', $action->id)->exists()) {
            return back()->withErrors(['action_code' => 'Ce code d\'action est déjà utilisé.'])->withInput();
        }

        $action->update([
            'name' => $data['action_name'],
            'code' => $actionCode,
        ]);

        return back()->with('success', 'Action mise à jour avec succès.');
    }

    /**
     * Remove the specified action from storage.
     */
    public function destroyAction(Action $action)
    {
        $action->delete();

        return back()->with('success', 'Action supprimée définitivement avec succès.');
    }
}
