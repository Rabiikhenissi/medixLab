@extends('layouts.admin')

@section('title', 'Mon Profil')

@section('page-title')
Mon <span style="color:#0066ff;">Profil</span>
@endsection

@section('page-subtitle')
Gérez vos informations personnelles et votre carte digitale.
@endsection

@section('content')

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ── FLIP CARD ── */
    .flip-card {
        perspective: 1200px;
        width: 100%;
        max-width: 380px;
        height: 240px;
        cursor: pointer;
    }
    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }
    .flip-card.flipped .flip-card-inner {
        transform: rotateY(180deg);
    }
    .flip-card-front, .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 18px;
        overflow: hidden;
    }
    .flip-card-front {
        background: linear-gradient(135deg, #0066ff 0%, #0044cc 100%);
        color: white;
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .flip-card-front .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .flip-card-front .card-logo {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -0.3px;
    }
    .flip-card-front .card-logo span {
        opacity: 0.7;
        font-weight: 400;
    }
    .flip-card-front .card-role {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: rgba(255,255,255,0.2);
        padding: 4px 10px;
        border-radius: 20px;
    }
    .flip-card-front .card-name {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.2;
    }
    .flip-card-front .card-code {
        font-family: 'SF Mono', 'Consolas', monospace;
        font-size: 13px;
        font-weight: 600;
        opacity: 0.85;
        background: rgba(255,255,255,0.15);
        padding: 5px 10px;
        border-radius: 8px;
        display: inline-block;
    }
    .flip-card-front .card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        opacity: 0.7;
    }
    .flip-card-back {
        background: #ffffff;
        border: 1px solid #e8eef4;
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 24px;
    }
    .flip-card-back .qr-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .flip-card-back .flip-hint {
        font-size: 10px;
        color: #94a3b8;
    }
    #qrcode {
        width: 140px;
        height: 140px;
    }
    #qrcode img {
        border-radius: 8px;
    }

    /* ── FORM CARD ── */
    .form-card {
        background: #ffffff;
        border: 1px solid #e8eef4;
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .form-card h3 {
        font-size: 14px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 20px 0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
    .form-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .form-field.full {
        grid-column: 1 / -1;
    }
    .form-field label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-field input, .form-field select, .form-field textarea {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: #0f172a;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        width: 100%;
    }
    .form-field input:focus, .form-field select:focus, .form-field textarea:focus {
        border-color: #0066ff;
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        background: white;
    }
    .form-field textarea {
        resize: none;
        min-height: 70px;
    }
    .form-field .readonly {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .btn-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #0066ff, #0052d4);
        color: white;
        font-size: 13px;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 3px 10px rgba(0, 102, 255, 0.3);
    }
    .btn-save:hover {
        background: linear-gradient(135deg, #0052d4, #0041af);
        box-shadow: 0 5px 16px rgba(0, 102, 255, 0.4);
    }
    .btn-cancel {
        padding: 10px 20px;
        background: white;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
    }
    .btn-cancel:hover {
        border-color: #94a3b8;
        color: #374151;
    }
    .section-divider {
        border: none;
        border-top: 1px solid #f1f5f9;
        margin: 24px 0;
    }
    .alert-success {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #166534;
        font-weight: 500;
    }
    .form-errors {
        background: #fff1f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 18px;
    }
    .form-errors ul {
        margin: 0;
        padding-left: 18px;
    }
    .form-errors li {
        font-size: 13px;
        color: #dc2626;
        font-weight: 500;
        margin-bottom: 2px;
    }
</style>

@if (session('success'))
    <div class="alert-success">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="form-errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="profile-grid anim anim-1">

    <!-- ── LEFT: DIGITAL CARD ── -->
    <div>
        <div class="flip-card" id="flipCard" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">

                <!-- FRONT -->
                <div class="flip-card-front">
                    <div class="card-header">
                        <div class="card-logo">Medix <span>eSanté</span></div>
                        <div class="card-role">
                            @if($user->doctor)
                                Médecin
                            @elseif($user->patient)
                                Patient
                            @elseif($user->staff)
                                Établissement
                            @else
                                Administrateur
                            @endif
                        </div>
                    </div>
                    <div class="card-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                    <div class="card-code">
                        @if($user->doctor)
                            {{ $user->doctor->doctor_code }}
                        @elseif($user->patient)
                            {{ $user->patient->patient_code }}
                        @elseif($user->staff)
                            {{ $user->staff->staff_code }}
                        @else
                            ADMIN
                        @endif
                    </div>
                    <div class="card-footer">
                        <span>{{ $user->email }}</span>
                        <span>{{ $user->phone ?? '' }}</span>
                    </div>
                </div>

                <!-- BACK -->
                <div class="flip-card-back">
                    <div class="qr-label">QR Code</div>
                    <div id="qrcode"></div>
                    <div class="flip-hint">Cliquez pour retourner</div>
                </div>

            </div>
        </div>

        <p style="font-size:11px; color:#94a3b8; text-align:center; margin-top:10px;">
            Cliquez sur la carte pour voir le QR Code
        </p>

        <!-- Quick info -->
        <div class="form-card" style="margin-top:20px;">
            <h3>Informations du compte</h3>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">Rôle</span>
                    <span style="color:#0f172a; font-weight:700;">
                        {{ $user->group->name ?? '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">Code unique</span>
                    <span style="color:#0066ff; font-weight:700; font-family:monospace;">
                        @if($user->doctor)
                            {{ $user->doctor->doctor_code }}
                        @elseif($user->patient)
                            {{ $user->patient->patient_code }}
                        @elseif($user->staff)
                            {{ $user->staff->staff_code }}
                        @else
                            —
                        @endif
                    </span>
                </div>
                @if($user->doctor && $user->doctor->speciality)
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">Spécialité</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->doctor->speciality }}</span>
                </div>
                @endif
                @if($user->patient && $user->patient->blood_group)
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">Groupe sanguin</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->patient->blood_group }}</span>
                </div>
                @endif
                @if($user->staff && $user->staff->laboratory)
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">Établissement</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->staff->laboratory->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── RIGHT: EDIT FORM ── -->
    <div>
        <div class="form-card">
            <h3>Modifier le profil</h3>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-field">
                        <label>Prénom</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="form-field">
                        <label>Nom</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="form-field">
                        <label>Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="form-field full">
                        <label>Adresse</label>
                        <textarea name="address">{{ old('address', $user->address) }}</textarea>
                    </div>

                    @if($user->doctor)
                    <div class="form-field">
                        <label>Spécialité</label>
                        <input type="text" name="speciality" value="{{ old('speciality', $user->doctor->speciality) }}">
                    </div>
                    <div class="form-field">
                        <label>Code médecin</label>
                        <input type="text" class="readonly" value="{{ $user->doctor->doctor_code }}" readonly>
                    </div>
                    @endif

                    @if($user->patient)
                    <div class="form-field">
                        <label>Groupe sanguin</label>
                        <select name="blood_group">
                            <option value="">—</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $user->patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Date de naissance</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->patient->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-field">
                        <label>Genre</label>
                        <select name="gender">
                            <option value="">—</option>
                            <option value="M" {{ old('gender', $user->patient->gender) === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ old('gender', $user->patient->gender) === 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Code patient</label>
                        <input type="text" class="readonly" value="{{ $user->patient->patient_code }}" readonly>
                    </div>
                    @endif

                    @if($user->staff)
                    <div class="form-field">
                        <label>Code staff</label>
                        <input type="text" class="readonly" value="{{ $user->staff->staff_code }}" readonly>
                    </div>
                    @if($user->staff->laboratory)
                    <div class="form-field">
                        <label>Établissement</label>
                        <input type="text" class="readonly" value="{{ $user->staff->laboratory->name }}" readonly>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">Enregistrer</button>
                </div>
            </form>
        </div>

        <div class="form-card" style="margin-top:20px;">
            <h3>Changer le mot de passe</h3>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-field full">
                        <label>Mot de passe actuel</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-field">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div class="form-field">
                        <label>Confirmer</label>
                        <input type="password" name="password_confirmation" required minlength="8">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">Modifier le mot de passe</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userCode = @json(
            $user->doctor ? $user->doctor->doctor_code :
            ($user->patient ? $user->patient->patient_code :
            ($user->staff ? $user->staff->staff_code : ''))
        );
        var appUrl = @json(config('app.url', env('APP_URL', 'http://localhost')));
        var role = @json(
            $user->doctor ? 'doctor' :
            ($user->patient ? 'patient' :
            ($user->staff ? 'center' : 'admin'))
        );

        var qrContent = userCode;
        if (role === 'patient' && appUrl) {
            qrContent = appUrl + '/doctor/scan/' + userCode;
        }

        if (userCode) {
            new QRCode(document.getElementById("qrcode"), {
                text: qrContent,
                width: 140,
                height: 140,
                colorDark: "#0066ff",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
</script>
@endsection
