<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
                                {{ __('layout.role_doctor') }}
                            @elseif($user->patient)
                                {{ __('layout.role_patient') }}
                            @elseif($user->staff)
                                {{ __('layout.role_center') }}
                            @else
                                {{ __('layout.admin') }}
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
                    <div class="flip-hint">{{ __('components.profile.click_to_flip') }}</div>
                </div>

            </div>
        </div>

        <p style="font-size:11px; color:#94a3b8; text-align:center; margin-top:10px;">
            {{ __('components.profile.click_card_for_qr') }}
        </p>

        <!-- Quick info -->
        <div class="form-card" style="margin-top:20px;">
            <h3>{{ __('components.profile.account_info') }}</h3>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">{{ __('components.profile.role') }}</span>
                    <span style="color:#0f172a; font-weight:700;">
                        {{ $user->group->name ?? '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">{{ __('components.profile.unique_code') }}</span>
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
                    <span style="color:#94a3b8; font-weight:600;">{{ __('doctor.specialty_label') }}</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->doctor->speciality }}</span>
                </div>
                @endif
                @if($user->patient && $user->patient->blood_group)
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">{{ __('auth.blood_group') }}</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->patient->blood_group }}</span>
                </div>
                @endif
                @if($user->staff && $user->staff->laboratory)
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:#94a3b8; font-weight:600;">{{ __('layout.role_center') }}</span>
                    <span style="color:#0f172a; font-weight:700;">{{ $user->staff->laboratory->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── RIGHT: EDIT FORM ── -->
    <div>
        <div class="form-card">
            <h3>{{ __('components.profile.edit_profile') }}</h3>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-field">
                        <label>{{ __('auth.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.last_name') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="form-field full">
                        <label>{{ __('auth.address') }}</label>
                        <textarea name="address">{{ old('address', $user->address) }}</textarea>
                    </div>

                    @if($user->doctor)
                    <div class="form-field">
                        <label>{{ __('doctor.specialty_label') }}</label>
                        <input type="text" name="speciality" value="{{ old('speciality', $user->doctor->speciality) }}">
                    </div>
                    <div class="form-field">
                        <label>{{ __('components.profile.doctor_code') }}</label>
                        <input type="text" class="readonly" value="{{ $user->doctor->doctor_code }}" readonly>
                    </div>
                    @endif

                    @if($user->patient)
                    <div class="form-field">
                        <label>{{ __('auth.blood_group') }}</label>
                        <select name="blood_group">
                            <option value="">—</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $user->patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.date_of_birth') }}</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->patient->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.gender_genre') }}</label>
                        <select name="gender">
                            <option value="">—</option>
                            <option value="M" {{ old('gender', $user->patient->gender) === 'M' ? 'selected' : '' }}>{{ __('components.profile.male') }}</option>
                            <option value="F" {{ old('gender', $user->patient->gender) === 'F' ? 'selected' : '' }}>{{ __('components.profile.female') }}</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>{{ __('components.profile.patient_code') }}</label>
                        <input type="text" class="readonly" value="{{ $user->patient->patient_code }}" readonly>
                    </div>
                    @endif

                    @if($user->staff)
                    <div class="form-field">
                        <label>{{ __('components.profile.staff_code') }}</label>
                        <input type="text" class="readonly" value="{{ $user->staff->staff_code }}" readonly>
                    </div>
                    @if($user->staff->laboratory)
                    <div class="form-field">
                        <label>{{ __('layout.role_center') }}</label>
                        <input type="text" class="readonly" value="{{ $user->staff->laboratory->name }}" readonly>
                    </div>
                    <div class="form-field full">
                        <label>{{ __('components.profile.lab_location') }}</label>
                        <div id="profileMapPicker" style="width:100%;height:280px;border-radius:12px;border:1px solid #e2e8f0;cursor:crosshair;"></div>
                        <input type="hidden" id="profileLat" name="latitude" value="{{ old('latitude', $user->staff->laboratory->latitude) }}">
                        <input type="hidden" id="profileLng" name="longitude" value="{{ old('longitude', $user->staff->laboratory->longitude) }}">
                        <p class="text-[10px] text-[#94a3b8] mt-1.5">
                            {{ __('components.profile.position') }} : <span id="profileCoords" class="font-bold text-[#1e293b]">
                                {{ $user->staff->laboratory->latitude && $user->staff->laboratory->longitude ? $user->staff->laboratory->latitude . ', ' . $user->staff->laboratory->longitude : __('components.profile.not_set') }}
                            </span>
                        </p>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>

        <div class="form-card" style="margin-top:20px;">
            <h3>{{ __('components.profile.change_password') }}</h3>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-field full">
                        <label>{{ __('components.profile.current_password') }}</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-field">
                        <label>{{ __('auth.new_password') }}</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div class="form-field">
                        <label>{{ __('common.confirm') }}</label>
                        <input type="password" name="password_confirmation" required minlength="8">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">{{ __('components.profile.update_password') }}</button>
                </div>
            </form>
        </div>
    </div>

</div>

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

        // Map picker for lab location
        var mapEl = document.getElementById('profileMapPicker');
        if (mapEl) {
            var latInput = document.getElementById('profileLat');
            var lngInput = document.getElementById('profileLng');
            var coordsSpan = document.getElementById('profileCoords');
            var initLat = parseFloat(latInput.value) || 36.7538;
            var initLng = parseFloat(lngInput.value) || 3.0588;
            var hasInitial = latInput.value && lngInput.value;

            var pickerMap = L.map('profileMapPicker').setView([initLat, initLng], hasInitial ? 14 : 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(pickerMap);

            var marker = null;
            if (hasInitial) {
                marker = L.marker([initLat, initLng]).addTo(pickerMap);
            }

            pickerMap.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);
                latInput.value = lat;
                lngInput.value = lng;
                coordsSpan.textContent = lat + ', ' + lng;
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(pickerMap);
                }
            });
        }
    });
</script>
