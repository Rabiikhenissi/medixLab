@extends('layouts.admin')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérez vos informations personnelles et mettez à jour votre mot de passe.')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr; gap: 24px; max-width: 800px; margin: 0 auto;">

        <div class="data-section anim anim-1" style="padding: 28px;">
            <h3 class="data-title"
                style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
                Détails du compte
            </h3>

            @if ($errors->any())
                <div class="form-errors" style="background: #fff1f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; color: #dc2626; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic User Fields -->
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Prénom <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                    </div>
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Nom de famille <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Adresse Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                    </div>
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px;">
                    <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Adresse Physique</label>
                    <textarea name="address" rows="3" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-family: inherit;">{{ old('address', $user->address) }}</textarea>
                </div>

                <!-- Role Specific Fields -->
                @if ($user->doctor)
                    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #e2e8f0; margin-bottom: 16px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 16px;">Informations Professionnelles (Médecin)</h4>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Code Unique Médecin (CNOM)</label>
                                <input type="text" value="{{ $user->doctor->doctor_code }}" readonly class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; font-size: 13px; font-family: monospace; font-weight: 700; color: #64748b;">
                            </div>
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Spécialité <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="speciality" value="{{ old('speciality', $user->doctor->speciality) }}" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                            </div>
                        </div>
                    </div>
                @elseif ($user->patient)
                    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #e2e8f0; margin-bottom: 16px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 16px;">Informations Médicales (Patient)</h4>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Code Patient Unique</label>
                                <input type="text" value="{{ $user->patient->patient_code }}" readonly class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; font-size: 13px; font-family: monospace; font-weight: 700; color: #64748b;">
                            </div>
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Groupe Sanguin</label>
                                <div style="position:relative;">
                                    <select name="blood_group" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; appearance: none;">
                                        <option value="">Non spécifié</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" {{ old('blood_group', $user->patient->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                    <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($user->staff)
                    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #e2e8f0; margin-bottom: 16px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 16px;">Établissement & Centre Médical</h4>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Nom du Laboratoire</label>
                                <input type="text" value="{{ $user->staff->laboratory->name ?? 'N/A' }}" readonly class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; font-size: 13px; font-weight: 600; color: #64748b;">
                            </div>
                            <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Code Employé (Staff Code)</label>
                                <input type="text" value="{{ $user->staff->staff_code }}" readonly class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; font-size: 13px; font-family: monospace; font-weight: 700; color: #64748b;">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Password Update Section -->
                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px dashed #e2e8f0; margin-bottom: 24px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 8px;">Modifier le mot de passe</h4>
                    <p style="font-size: 12px; color: #64748b; margin-top: 0; margin-bottom: 16px;">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 caractères" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #475569;">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Répéter le mot de passe" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <button type="submit" class="btn-submit" style="padding: 10px 20px; font-size: 13px; font-weight: 600; background: #0066ff; color: white; border: none; border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                        Mettre à jour mon profil
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
