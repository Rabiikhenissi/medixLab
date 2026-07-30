<?php

namespace App\Http\Controllers;

use App\Models\MachineConfiguration;
use App\Services\MachineService;
use Illuminate\Http\Request;

class MachineConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->staff) {
                return redirect()->route('home');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $lab = auth()->user()->staff->laboratory;
        $configs = $lab->machineConfigurations()->active()->latest()->get();
        return view('center.machine-configurations.index', compact('configs', 'lab'));
    }

    public function create()
    {
        return view('center.machine-configurations.form', ['config' => null]);
    }

    public function store(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http_json,hl7_mllp',
            'mllp_port' => 'nullable|integer|min:1|max:65535',
            'api_key' => 'nullable|string|max:255',
            'timeout' => 'required|integer|min:1|max:300',
            'enabled' => 'boolean',
        ]);

        $validated['labo_id'] = $lab->id;
        $validated['enabled'] = $request->boolean('enabled');

        MachineConfiguration::create($validated);

        return redirect()->route('center.machine-configurations.index')
            ->with('success', 'Configuration machine ajoutée.');
    }

    public function edit(MachineConfiguration $machineConfiguration)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }
        return view('center.machine-configurations.form', ['config' => $machineConfiguration]);
    }

    public function update(Request $request, MachineConfiguration $machineConfiguration)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http_json,hl7_mllp',
            'mllp_port' => 'nullable|integer|min:1|max:65535',
            'api_key' => 'nullable|string|max:255',
            'timeout' => 'required|integer|min:1|max:300',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $request->boolean('enabled');

        $machineConfiguration->update($validated);

        return redirect()->route('center.machine-configurations.index')
            ->with('success', 'Configuration machine mise à jour.');
    }

    public function destroy(MachineConfiguration $machineConfiguration)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }

        $machineConfiguration->update(['is_archive' => true]);

        return redirect()->route('center.machine-configurations.index')
            ->with('success', 'Configuration machine supprimée.');
    }

    public function test(MachineConfiguration $machineConfiguration)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }

        $service = new MachineService($machineConfiguration);

        $online = $service->isOnline();
        $info = $service->getStatus();

        return response()->json([
            'online' => $online,
            'info' => $info,
            'config' => [
                'name' => $machineConfiguration->name,
                'url' => $machineConfiguration->getBaseUrl(),
                'protocol' => $machineConfiguration->protocol,
            ],
        ]);
    }
}
