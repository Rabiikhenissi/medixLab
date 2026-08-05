<?php

namespace App\Http\Controllers;

use App\Models\Labo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaboratoryController extends Controller
{
    /**
     * Display a listing of laboratories.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $showArchived = $request->boolean('show_archived');

        $query = Labo::query();

        // hide archived laboratories unless explicitly requested
        if (! $showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        // narrow results by search keyword
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $laboratories = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        return view('admin.laboratories.index', [
            'laboratories' => $laboratories,
            'search' => $search,
            'showArchived' => $showArchived,
        ]);
    }

    /**
     * Show the form for creating a new laboratory.
     */
    public function create()
    {
        return view('admin.laboratories.create');
    }

    /**
     * Store a newly created laboratory in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // validate the laboratory fields
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $data['is_archive'] = false;
        Labo::create($data);

        return redirect()->route('admin.laboratories.index')->with('success', 'Laboratoire créé avec succès.');
    }

    /**
     * Show the form for editing the specified laboratory.
     *
     * @return View
     */
    public function edit(Labo $laboratory)
    {
        return view('admin.laboratories.edit', [
            'laboratory' => $laboratory,
        ]);
    }

    /**
     * Update the specified laboratory in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Labo $laboratory)
    {
        // validate the laboratory fields
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $laboratory->update($data);

        return redirect()->route('admin.laboratories.index')->with('success', 'Laboratoire mis à jour avec succès.');
    }

    /**
     * Remove the specified laboratory from storage (toggle archive).
     *
     * @return RedirectResponse
     */
    public function destroy(Labo $laboratory)
    {
        $laboratory->update(['is_archive' => ! $laboratory->is_archive]);

        $message = $laboratory->is_archive
            ? 'Laboratoire archivé avec succès.'
            : 'Laboratoire restauré avec succès.';

        return redirect()->route('admin.laboratories.index')->with('success', $message);
    }

    /**
     * Permanently remove the specified laboratory from storage.
     *
     * @return RedirectResponse
     */
    public function forceDelete(Labo $laboratory)
    {
        $laboratory->delete();

        return redirect()->route('admin.laboratories.index')->with('success', 'Laboratoire supprimé définitivement.');
    }
}
