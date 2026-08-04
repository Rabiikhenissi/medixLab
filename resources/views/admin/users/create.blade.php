@extends('layouts.admin')

@section('title', 'Creer un Utilisateur')

@section('page-title', 'Nouveau Compte Utilisateur')
@section('page-subtitle', 'Saisissez les informations de l\'utilisateur et attribuez-lui un role de securite.')

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title" style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            Informations Personnelles & Role
        </h3>

        @if($errors->any())
            <div class="form-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Prenom<span class="required-star">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Ex: Jean" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Nom<span class="required-star">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Ex: Dupont" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Adresse Email<span class="required-star">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Ex: jean.dupont@email.com" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Telephone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Ex: 55123456" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Role / Groupe de Securite<span class="required-star">*</span></label>
                    <div style="position:relative;">
                        <select name="group_id" id="group_id" required class="form-control" onchange="toggleLabField(); updateRoleTableHint()">
                            <option value="">Selectionner un groupe...</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" data-role-table="{{ $group->role_table }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                    <div id="role-table-hint" style="margin-top:8px;font-size:12px;color:#64748b;"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse de residence</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Ex: 15 Rue de Paris, Tunis" class="form-control">
                </div>
            </div>

            <!-- Laboratory Select (Conditional) -->
            <div class="form-group" id="laboratory-group" style="display: none; margin-bottom: 16px;">
                <label class="form-label">Laboratoire Associé<span class="required-star">*</span></label>
                <div style="position:relative;">
                    <select name="laboratory_id" id="laboratory_id" class="form-control">
                        <option value="">Sélectionner un laboratoire...</option>
                        @foreach($laboratories as $labo)
                            <option value="{{ $labo->id }}" {{ old('laboratory_id') == $labo->id ? 'selected' : '' }}>{{ $labo->name }} ({{ $labo->city }})</option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row" style="margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">Mot de Passe<span class="required-star">*</span></label>
                    <div class="pw-input-wrap" style="position:relative;">
                        <input type="password" name="password" id="pw1" required placeholder="Saisir 8 caracteres minimum" class="form-control" style="padding-right: 42px;">
                        <button type="button" tabindex="-1" onclick="togglePw(this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex;align-items:center;justify-content:center;border-radius:6px;">
                            <svg class="pw-eye" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg class="pw-eye-off" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmer le Mot de Passe<span class="required-star">*</span></label>
                    <div class="pw-input-wrap" style="position:relative;">
                        <input type="password" name="password_confirmation" id="pw2" required placeholder="Confirmer le mot de passe" class="form-control" style="padding-right: 42px;">
                        <button type="button" tabindex="-1" onclick="togglePw(this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex;align-items:center;justify-content:center;border-radius:6px;">
                            <svg class="pw-eye" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg class="pw-eye-off" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Creer l'utilisateur</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function toggleLabField() {
        var groupSelect = document.getElementById('group_id');
        var labGroup = document.getElementById('laboratory-group');
        var labSelect = document.getElementById('laboratory_id');
        
        if (!groupSelect) return;
        
        var selectedOption = groupSelect.options[groupSelect.selectedIndex];
        var roleTable = selectedOption ? selectedOption.getAttribute('data-role-table') : '';
        
        if (roleTable === 'staff') {
            labGroup.style.display = 'flex';
            labSelect.setAttribute('required', 'required');
        } else {
            labGroup.style.display = 'none';
            labSelect.removeAttribute('required');
        }
    }

    function togglePw(btn) {
        var input = btn.previousElementSibling;
        if (!input || input.tagName !== 'INPUT') return;
        var showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        var eye = btn.querySelector('.pw-eye');
        var eyeOff = btn.querySelector('.pw-eye-off');
        if (eye) eye.style.display = showing ? '' : 'none';
        if (eyeOff) eyeOff.style.display = showing ? 'none' : '';
        input.focus();
    }

    var roleTableLabels = {
        'admin': 'Table de profil : Administrateurs (admins)',
        'doctor': 'Table de profil : Médecins (doctors)',
        'patient': 'Table de profil : Patients (patients)',
        'staff': 'Table de profil : Personnel du Centre (staff)'
    };

    function updateRoleTableHint() {
        var groupSelect = document.getElementById('group_id');
        var hint = document.getElementById('role-table-hint');
        if (!groupSelect || !hint) return;

        var selectedOption = groupSelect.options[groupSelect.selectedIndex];
        var roleTable = selectedOption ? selectedOption.getAttribute('data-role-table') : '';

        if (roleTable && roleTableLabels[roleTable]) {
            hint.textContent = roleTableLabels[roleTable];
        } else {
            hint.textContent = '';
        }
    }
    
    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        toggleLabField();
        updateRoleTableHint();
    });
</script>
@endsection
