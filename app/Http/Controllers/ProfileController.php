<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['doctor', 'patient', 'staff.laboratory', 'group']);

        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        // Update role-specific fields
        if ($user->doctor) {
            $request->validate([
                'speciality' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
            $user->doctor->update([
                'speciality' => $request->speciality ?? $user->doctor->speciality,
                'latitude' => $request->latitude !== null ? $request->latitude : $user->doctor->latitude,
                'longitude' => $request->longitude !== null ? $request->longitude : $user->doctor->longitude,
            ]);
        }

        if ($user->patient) {
            $request->validate([
                'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:M,F',
            ]);
            $user->patient->update([
                'blood_group' => $request->blood_group ?? $user->patient->blood_group,
                'date_of_birth' => $request->date_of_birth ?? $user->patient->date_of_birth,
                'gender' => $request->gender ?? $user->patient->gender,
            ]);
        }

        if ($user->staff && $user->staff->laboratory) {
            $request->validate([
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
            $user->staff->laboratory->update([
                'latitude' => $request->latitude !== null ? $request->latitude : $user->staff->laboratory->latitude,
                'longitude' => $request->longitude !== null ? $request->longitude : $user->staff->laboratory->longitude,
            ]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
