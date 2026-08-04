@extends('layouts.admin')

@section('title', 'Modifier le Rôle')

@section('page-title', 'Modifier le Rôle : ' . $group->name)
@section('page-subtitle', 'Mettez à jour le nom, le code du rôle et ajustez ses habilitations de sécurité.')

@section('content')
<style>
    .group-create-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
    .group-card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; }
    .group-card-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .group-card-icon svg { width: 20px; height: 20px; }
    .group-card-icon.blue { background: #eff6ff; color: #0066ff; }
    .group-card-icon.green { background: #f0fdf4; color: #16a34a; }
    .group-card-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
    .group-card-desc { font-size: 12px; color: #94a3b8; margin: 2px 0 0 0; }
    .group-form-card { padding: 24px; }
    .code-input-wrap { display: flex; align-items: stretch; border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; transition: border-color 0.2s, box-shadow 0.2s; }
    .code-input-wrap:focus-within { border-color: #0066ff; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    .code-prefix { display: flex; align-items: center; padding: 0 12px; background: #f1f5f9; font-size: 13px; font-weight: 600; color: #94a3b8; font-family: 'SF Mono','Consolas',monospace; border-right: 1px solid #e2e8f0; white-space: nowrap; user-select: none; }
    .code-input { border: none !important; border-radius: 0 !important; box-shadow: none !important; background: transparent; }
    .code-input:focus { box-shadow: none !important; }
    .field-hint { display: block; font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .group-summary-card { padding: 24px; position: sticky; top: 76px; }
    .summary-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; }
    .summary-label { font-size: 12px; color: #64748b; font-weight: 500; }
    .summary-value { font-size: 13px; color: #0f172a; font-weight: 600; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .summary-value.mono { font-family: 'SF Mono','Consolas',monospace; font-size: 12px; color: #0066ff; }
    .summary-divider { height: 1px; background: #f1f5f9; margin: 6px 0; }
    .summary-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 24px; padding: 0 8px; border-radius: 8px; font-size: 12px; font-weight: 700; }
    .summary-badge.blue { background: #eff6ff; color: #0066ff; }
    .summary-badge.green { background: #f0fdf4; color: #16a34a; }
    .summary-progress-wrap { margin-top: 6px; }
    .summary-progress-bar { height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden; margin-bottom: 6px; }
    .summary-progress-fill { height: 100%; background: linear-gradient(90deg, #0066ff, #00aaff); border-radius: 3px; transition: width 0.3s ease; width: 0%; }
    .summary-progress-text { font-size: 11px; color: #94a3b8; }
    .group-matrix-header { padding: 20px 24px; border-bottom: 1px solid #e8eef4; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .matrix-controls { display: flex; align-items: center; gap: 8px; }
    .matrix-search-wrap { position: relative; }
    .btn-matrix-action { display: inline-flex; align-items: center; gap: 5px; padding: 7px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s; font-family: 'Inter',sans-serif; white-space: nowrap; }
    .btn-matrix-action:hover { border-color: #0066ff; color: #0066ff; background: #eff6ff; }
    .feature-card { transition: border-color 0.2s, box-shadow 0.2s; }
    .feature-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
    .feature-card-top { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 4px; }
    .feature-card-actions { display: flex; align-items: center; gap: 8px; }
    .feature-count { font-size: 11px; font-weight: 600; color: #94a3b8; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; }
    .feature-toggle-btn { width: 26px; height: 26px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: all 0.15s; padding: 0; }
    .feature-toggle-btn svg { width: 12px; height: 12px; }
    .feature-toggle-btn:hover { border-color: #0066ff; color: #0066ff; background: #eff6ff; }
    .feature-actions-list { display: flex; flex-direction: column; gap: 2px; }
    .feature-empty { font-size: 12px; color: #cbd5e1; font-style: italic; padding: 8px 0; }
    .action-chip { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; cursor: pointer; transition: background 0.15s; font-size: 13px; color: #475569; font-weight: 500; }
    .action-chip:hover { background: #f8fafc; }
    .action-chip input[type="checkbox"] { accent-color: #0066ff; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0; }
    .action-chip-label { user-select: none; }
    .action-chip:has(input:checked) { background: #eff6ff; }
    .action-chip:has(input:checked) .action-chip-label { color: #1e40af; font-weight: 600; }
    .group-sticky-footer { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e8eef4; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; margin: 0 -36px -48px; padding-left: 36px; padding-right: 36px; z-index: 50; box-shadow: 0 -4px 20px rgba(0,0,0,0.04); }
    .footer-perm-count { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #0066ff; }
    .footer-perm-count svg { width: 14px; height: 14px; }
    .group-footer-right { display: flex; gap: 10px; }
    .btn-submit svg { width: 15px; height: 15px; }
    @media (max-width: 1024px) {
        .group-create-grid { grid-template-columns: 1fr; }
        .group-summary-card { position: static; }
        .matrix-controls { width: 100%; flex-wrap: wrap; }
        .matrix-search-wrap { flex: 1; }
        .group-sticky-footer { margin: 0 -16px -32px; padding-left: 16px; padding-right: 16px; }
    }
    @media (max-width: 640px) {
        .group-matrix-header { flex-direction: column; align-items: stretch; }
        .group-footer-left { display: none; }
    }
</style>

<form action="{{ route('admin.groups.update', $group) }}" method="POST" id="group-form">
    @csrf
    @method('PUT')

    <div class="group-create-grid anim anim-1">
        <div class="data-section group-form-card">
            <div class="group-card-header">
                <div class="group-card-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <h3 class="group-card-title">Identité du Rôle</h3>
                    <p class="group-card-desc">Nom public et code technique unique</p>
                </div>
            </div>

            @if($errors->any())
                <div class="form-errors" style="margin-bottom: 20px;">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Nom du Rôle <span class="required-star">*</span></label>
                <input type="text" name="name" value="{{ old('name', $group->name) }}" required
                    placeholder="Ex: Directeur de Laboratoire" class="form-control"
                    id="role-name" data-initial="{{ $group->name }}" oninput="updateSummary()">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Code de sécurité (Unique) <span class="required-star">*</span></label>
                <div class="code-input-wrap">
                    
                    <input type="text" name="code" value="{{ old('code', $group->code) }}" required
                        placeholder="directeur-labo" class="form-control code-input"
                        id="role-code" data-initial="{{ $group->code }}" oninput="updateSummary()">
                </div>
                <span class="field-hint">Sera automatiquement formaté en slug (minuscules, tirets).</span>
            </div>

            <div class="form-group" style="margin-top: 18px;">
                <label class="form-label">Type de rôle (Optionnel)</label>
                <div style="position:relative;">
                    <select name="role_table" id="role-table" class="form-control" data-initial="{{ $group->role_table ?? '' }}" onchange="updateSummary()">
                        <option value="">Aucune (profil générique)</option>
                        @foreach(['admin' => 'Admin', 'doctor' => 'Docteur', 'patient' => 'Patient', 'staff' => 'Centre / Laboratoire'] as $value => $label)
                            <option value="{{ $value }}" {{ (old('role_table') ?? $group->role_table) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                <span class="field-hint">Détermine dans quelle table les utilisateurs de ce rôle seront ajoutés (admins, doctors, patients, staff).</span>
            </div>
        </div>

        @php
            $initialActionIds = $group->actions->pluck('id')->toArray();
        @endphp

        <div class="data-section group-form-card group-summary-card">
            <div class="group-card-header">
                <div class="group-card-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                </div>
                <div>
                    <h3 class="group-card-title">Résumé en Direct</h3>
                    <p class="group-card-desc">Aperçu de votre configuration</p>
                </div>
            </div>

            <div class="summary-row">
                <span class="summary-label">Nom</span>
                <span class="summary-value" id="summary-name">{{ $group->name }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Code</span>
                <span class="summary-value mono" id="summary-code">{{ $group->code }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Type de rôle</span>
                <span class="summary-value" id="summary-role-table">
                    {{ ($labels = ['admin' => 'Admin', 'doctor' => 'Docteur', 'patient' => 'Patient', 'staff' => 'Centre / Laboratoire'])[$group->role_table] ?? '—' }}
                </span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row">
                <span class="summary-label">Modules couverts</span>
                <span class="summary-badge blue" id="summary-features">0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Actions sélectionnées</span>
                <span class="summary-badge green" id="summary-actions">0</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-progress-wrap">
                <div class="summary-progress-bar">
                    <div class="summary-progress-fill" id="summary-progress"></div>
                </div>
                <span class="summary-progress-text" id="summary-progress-text">0% des actions disponibles</span>
            </div>
        </div>
    </div>

    <div class="data-section anim anim-2" style="margin-top: 20px;">
        <div class="group-matrix-header">
            <div>
                <h3 class="data-title" style="margin: 0;">Matrice des Habilitations</h3>
                <p style="font-size: 12px; color: #94a3b8; margin: 4px 0 0 0;">Cochez les actions que ce rôle aura le droit d'effectuer.</p>
            </div>
            <div class="matrix-controls">
                <div class="matrix-search-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" id="permission-search" placeholder="Rechercher une action..."
                        class="filter-input" style="width: 220px;" oninput="filterPermissions()">
                </div>
                <button type="button" class="btn-matrix-action" onclick="selectAll()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tout cocher
                </button>
                <button type="button" class="btn-matrix-action" onclick="deselectAll()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tout décocher
                </button>
            </div>
        </div>

        <div class="permissions-grid" id="permissions-grid">
            @foreach($features as $feature)
                <div class="feature-card" data-feature-id="{{ $feature->id }}">
                    <div class="feature-card-top">
                        <div class="feature-name">{{ $feature->name }}</div>
                        <div class="feature-card-actions">
                            <span class="feature-count" data-count="{{ $feature->id }}">0 / {{ $feature->actions->count() }}</span>
                            <button type="button" class="feature-toggle-btn" onclick="toggleFeatureAll({{ $feature->id }})">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="feature-actions-list">
                        @forelse($feature->actions as $action)
                            <label class="action-chip" data-action-name="{{ strtolower($action->name) }}">
                                <input type="checkbox" name="actions[]" value="{{ $action->id }}"
                                    class="action-check"
                                    data-feature="{{ $feature->id }}"
                                    data-initial="{{ in_array($action->id, $groupActionIds) ? '1' : '0' }}"
                                    {{ (is_array(old('actions')) && in_array($action->id, old('actions'))) || (!is_array(old('actions')) && in_array($action->id, $groupActionIds)) ? 'checked' : '' }}
                                    onchange="updateSummary()">
                                <span class="action-chip-label">{{ $action->name }}</span>
                            </label>
                        @empty
                            <div class="feature-empty">Aucune action enregistrée</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        @if($features->isEmpty())
            <div class="empty-state" style="padding: 40px 20px;">
                <div class="empty-state-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="width:26px;height:26px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <h3>Aucune fonctionnalité disponible</h3>
                <p>Créez d'abord des modules et des actions dans la section Fonctionnalités.</p>
            </div>
        @endif
    </div>

    <div class="group-sticky-footer anim anim-3">
        <div class="group-footer-left">
            <span class="footer-perm-count">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                <span id="footer-count">0</span> habilitation(s) sélectionnée(s)
            </span>
        </div>
        <div class="group-footer-right">
            <button type="button" class="btn-cancel" onclick="resetForm()" style="display: inline-flex; align-items: center; gap: 5px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                Réinitialiser
            </button>
            <a href="{{ route('admin.groups.index') }}" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-submit">
                
                Enregistrer les modifications
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function updateSummary() {
        var name = document.getElementById('role-name').value.trim();
        var code = document.getElementById('role-code').value.trim();
        var roleSelect = document.getElementById('role-table');
        var roleLabel = roleSelect && roleSelect.selectedIndex >= 0
            ? roleSelect.options[roleSelect.selectedIndex].text
            : '—';
        var checks = document.querySelectorAll('.action-check');
        var total = checks.length;
        var selected = document.querySelectorAll('.action-check:checked').length;

        document.getElementById('summary-name').textContent = name || '—';
        document.getElementById('summary-code').textContent = code || '—';
        document.getElementById('summary-role-table').textContent = roleLabel || '—';
        document.getElementById('summary-actions').textContent = selected;
        document.getElementById('footer-count').textContent = selected;

        var featureIds = {};
        checks.forEach(function(c) { if (c.checked) featureIds[c.dataset.feature] = true; });
        document.getElementById('summary-features').textContent = Object.keys(featureIds).length;

        var pct = total > 0 ? Math.round((selected / total) * 100) : 0;
        document.getElementById('summary-progress').style.width = pct + '%';
        document.getElementById('summary-progress-text').textContent = pct + '% des actions disponibles';

        document.querySelectorAll('[data-count]').forEach(function(el) {
            var fid = el.dataset.count;
            var fTotal = document.querySelectorAll('.action-check[data-feature="' + fid + '"]').length;
            var fSelected = document.querySelectorAll('.action-check[data-feature="' + fid + '"]:checked').length;
            el.textContent = fSelected + ' / ' + fTotal;
        });
    }

    function toggleFeatureAll(featureId) {
        var checks = document.querySelectorAll('.action-check[data-feature="' + featureId + '"]');
        var allChecked = Array.from(checks).every(function(c) { return c.checked; });
        checks.forEach(function(c) { c.checked = !allChecked; });
        updateSummary();
    }

    function selectAll() {
        document.querySelectorAll('.action-check').forEach(function(c) { c.checked = true; });
        updateSummary();
    }

    function deselectAll() {
        document.querySelectorAll('.action-check').forEach(function(c) { c.checked = false; });
        updateSummary();
    }

    function filterPermissions() {
        var q = document.getElementById('permission-search').value.toLowerCase().trim();
        document.querySelectorAll('.feature-card').forEach(function(card) {
            var featureName = card.querySelector('.feature-name').textContent.toLowerCase();
            var chips = card.querySelectorAll('.action-chip');
            var anyVisible = false;

            chips.forEach(function(chip) {
                var actionName = chip.dataset.actionName || '';
                var match = !q || actionName.indexOf(q) !== -1 || featureName.indexOf(q) !== -1;
                chip.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });

            card.style.display = (!q || featureName.indexOf(q) !== -1 || anyVisible) ? '' : 'none';
        });
    }

    function resetForm() {
        document.getElementById('role-name').value = document.getElementById('role-name').dataset.initial;
        document.getElementById('role-code').value = document.getElementById('role-code').dataset.initial;
        var roleSelect = document.getElementById('role-table');
        roleSelect.value = roleSelect.dataset.initial || '';
        document.querySelectorAll('.action-check').forEach(function(c) {
            c.checked = c.dataset.initial === '1';
        });
        document.getElementById('permission-search').value = '';
        filterPermissions();
        updateSummary();
    }

    document.addEventListener('DOMContentLoaded', updateSummary);
</script>
@endsection
