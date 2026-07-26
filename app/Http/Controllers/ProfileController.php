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

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($user->doctor) {
            $rules['speciality'] = 'nullable|string|max:255';
            $rules['latitude'] = 'nullable|numeric|between:-90,90';
            $rules['longitude'] = 'nullable|numeric|between:-180,180';
        }

        if ($user->patient) {
            $rules['blood_group'] = 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-';
            $rules['date_of_birth'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:M,F';
        }

        if ($user->staff && $user->staff->laboratory) {
            $rules['latitude'] = 'nullable|numeric|between:-90,90';
            $rules['longitude'] = 'nullable|numeric|between:-180,180';
        }

        $data = $request->validate($rules);

        $user->update(collect($data)->only(['first_name', 'last_name', 'phone', 'address', 'email'])->toArray());

        if ($user->doctor) {
            $user->doctor->update(collect($data)->only(['speciality', 'latitude', 'longitude'])->toArray());
        }

        if ($user->patient) {
            $user->patient->update(collect($data)->only(['blood_group', 'date_of_birth', 'gender'])->toArray());
        }

        if ($user->staff && $user->staff->laboratory) {
            $user->staff->laboratory->update(collect($data)->only(['latitude', 'longitude'])->toArray());
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
