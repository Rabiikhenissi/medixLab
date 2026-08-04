<?php

namespace App\Http\Controllers;

use App\Models\MachineConfiguration;
use App\Services\MachineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MachineConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || ! auth()->user()->staff) {
                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    /**
     * List the machine configurations of the current laboratory.
     *
     * @return View
     */
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

    /**
     * Store a new machine configuration for the current laboratory.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;

        // validate the connection settings
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http_json,hl7_mllp,serial_hl7',
            'mllp_port' => 'nullable|integer|min:1|max:65535',
            'serial_port' => 'nullable|string|max:50',
            'baud_rate' => 'nullable|integer|min:110|max:115200',
            'data_bits' => 'nullable|integer|in:5,6,7,8',
            'stop_bits' => 'nullable|integer|in:1,2',
            'parity' => 'nullable|in:N,E,O',
            'api_key' => 'nullable|string|max:255',
            'timeout' => 'required|integer|min:1|max:300',
            'enabled' => 'boolean',
        ]);

        // attach the laboratory and normalize the enabled flag
        $validated['labo_id'] = $lab->id;
        $validated['enabled'] = $request->boolean('enabled');

        MachineConfiguration::create($validated);

        // redirect back to the configurations list
        return redirect()->route('center.machine-configurations.index')
            ->with('success', 'Configuration machine ajoutée.');
    }

    /**
     * Show the form to edit a machine configuration.
     *
     * @return View
     */
    public function edit(MachineConfiguration $machineConfiguration)
    {
        // only the owning laboratory may edit the configuration
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }

        return view('center.machine-configurations.form', ['config' => $machineConfiguration]);
    }

    /**
     * Update a machine configuration.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, MachineConfiguration $machineConfiguration)
    {
        // only the owning laboratory may update the configuration
        $lab = auth()->user()->staff->laboratory;
        if ($machineConfiguration->labo_id !== $lab->id) {
            abort(403);
        }

        // validate the connection settings
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http_json,hl7_mllp,serial_hl7',
            'mllp_port' => 'nullable|integer|min:1|max:65535',
            'serial_port' => 'nullable|string|max:50',
            'baud_rate' => 'nullable|integer|min:110|max:115200',
            'data_bits' => 'nullable|integer|in:5,6,7,8',
            'stop_bits' => 'nullable|integer|in:1,2',
            'parity' => 'nullable|in:N,E,O',
            'api_key' => 'nullable|string|max:255',
            'timeout' => 'required|integer|min:1|max:300',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $request->boolean('enabled');

        $machineConfiguration->update($validated);

        // redirect back to the configurations list
        return redirect()->route('center.machine-configurations.index')
            ->with('success', 'Configuration machine mise à jour.');
    }

    /**
     * Archive a machine configuration.
     *
     * @return RedirectResponse
     */
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

    /**
     * Test the connection to a machine and return its status as JSON.
     *
     * @return JsonResponse
     */
    public function test(MachineConfiguration $machineConfiguration)
    {
        // only the owning laboratory may test the configuration
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
