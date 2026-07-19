@extends('layouts.admin')

@section('title', 'Modifier le Module')

@section('page-title', 'Modifier le Module')
@section('page-subtitle', 'Mettez a jour la configuration du module et pilotez ses actions de securite.')

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title"
            style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            Configuration Generale
        </h3>

        @if ($errors->any() && !$errors->has('action_name') && !$errors->has('action_code'))
            <div class="form-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.features.update', $feature) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom du Module<span class="required-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $feature->name) }}" required
                        placeholder="Ex: Gestion des Utilisateurs" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Code du Module (Unique)<span class="required-star">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $feature->code) }}" required
                        placeholder="Ex: users-management" class="form-control"
                        style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Route de Navigation (Nom de route Laravel)</label>
                    <input type="text" name="route_name" value="{{ old('route_name', $feature->route_name) }}"
                        placeholder="Ex: admin.users.index" class="form-control" style="font-family:monospace;">
                </div>
                <div class="form-group">
                    <label class="form-label">Permission requise pour voir ce module (Action code)</label>
                    <input type="text" name="view_permission"
                        value="{{ old('view_permission', $feature->view_permission) }}"
                        placeholder="Ex: view-users" class="form-control" style="font-family:monospace;">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Afficher dans le sidebar ?<span class="required-star">*</span></label>
                    <div style="position:relative;">
                        <select name="is_sidebar" required class="form-control">
                            <option value="1" {{ old('is_sidebar', $feature->is_sidebar) ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ !old('is_sidebar', $feature->is_sidebar) ? 'selected' : '' }}>Non</option>
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ordre d'affichage dans la barre de navigation<span
                            class="required-star">*</span></label>
                    <input type="number" name="order" value="{{ old('order', $feature->order) }}" required min="0"
                        class="form-control">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">Icône du module</label>

                <input type="hidden" name="icon" id="selectedIcon"
                    value="{{ old('icon', $feature->icon ?? 'users') }}">

                <button type="button" class="selected-icon-box" onclick="openIconPicker()">
                    <div id="selectedIconPreview">
                        <x-dynamic-component :component="'heroicon-o-' . old('icon', $feature->icon ?? 'users')" class="icon-svg" />
                    </div>
                    <span id="selectedIconName">
                        {{ old('icon', $feature->icon ?? 'users') }}
                    </span>
                    <span>▼</span>
                </button>
            </div>

            <!-- Actions Section -->
            <div style="margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 24px;">
                <h3 class="data-title" style="margin-top: 0; margin-bottom: 20px; font-size: 15px;">
                    Actions Habilitées pour ce Module
                </h3>

                @if ($errors->has('action_name') || $errors->has('action_code'))
                    <div class="form-errors">
                        <ul>
                            @error('action_name')
                                <li>{{ $message }}</li>
                            @enderror
                            @error('action_code')
                                <li>{{ $message }}</li>
                            @enderror
                        </ul>
                    </div>
                @endif

                <!-- Add new action inline bar -->
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Nom de l'Action</label>
                        <input type="text" id="new-action-name" placeholder="Ex: Lire Examens" class="form-control" style="width:100%;">
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" style="font-size: 12px; margin-bottom: 4px;">Code de l'Action</label>
                        <input type="text" id="new-action-code" placeholder="Ex: view-exams" class="form-control" style="width:100%; font-family: monospace;">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="button" onclick="submitNewAction()" class="btn-submit" style="padding: 10px 16px; font-size: 13px; white-space: nowrap;">
                            Ajouter
                        </button>
                    </div>
                </div>

                <!-- Hidden form for adding action via POST -->
                <form id="add-action-form" action="{{ route('admin.features.actions.store', $feature) }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="action_name" id="hidden-action-name">
                    <input type="hidden" name="action_code" id="hidden-action-code">
                </form>

                <!-- Actions Table -->
                <div class="data-section" style="margin-bottom: 0;">
                    <table class="data-table" id="actions-table">
                        <thead>
                            <tr>
                                <th>Nom de l'Action</th>
                                <th>Code de l'Action</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="actions-tbody">
                            @forelse($actions as $action)
                                <!-- View Row -->
                                <tr id="row-view-{{ $action->id }}">
                                    <td style="font-weight: 600; font-size: 13px; color: #1e293b;">
                                        {{ $action->name }}
                                    </td>
                                    <td>
                                        <span class="exam-code" style="color: #4f46e5; background: #e0e7ff;">{{ $action->code }}</span>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                            <button type="button" onclick="showEditForm({{ $action->id }})" class="table-action-btn" title="Modifier" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748b;">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                            <!-- Hidden delete form -->
                                            <form id="delete-form-{{ $action->id }}" action="{{ route('admin.actions.destroy', $action) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button"
                                                onclick="confirmDeleteAction({{ $action->id }}, '{{ addslashes($action->name) }}')"
                                                class="table-action-btn archive-btn"
                                                title="Supprimer"
                                                style="background:none;border:none;cursor:pointer;padding:4px;color:#ef4444;">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Row (inline) -->
                                <tr id="row-edit-{{ $action->id }}" style="display:none;">
                                    <td colspan="3">
                                        <form action="{{ route('admin.actions.update', $action) }}" method="POST"
                                            style="display: flex; gap: 12px; align-items: center; width: 100%; padding: 4px 0;">
                                            @csrf
                                            @method('PUT')
                                            <div style="flex: 1;">
                                                <input type="text" name="action_name" value="{{ $action->name }}" required
                                                    class="form-control" style="width:100%; padding: 6px 10px; font-size:13px;">
                                            </div>
                                            <div style="flex: 1;">
                                                <input type="text" name="action_code" value="{{ $action->code }}" required
                                                    class="form-control" style="width:100%; padding: 6px 10px; font-size:13px; font-family: monospace;">
                                            </div>
                                            <div style="display: flex; gap: 6px;">
                                                <button type="submit" class="btn-submit" style="padding: 6px 12px; font-size: 12px; white-space: nowrap;">
                                                    Enregistrer
                                                </button>
                                                <button type="button" onclick="hideEditForm({{ $action->id }})" class="btn-cancel" style="padding: 6px 12px; font-size: 12px; white-space: nowrap;">
                                                    Annuler
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-actions-row">
                                    <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                                        Aucune action définie pour ce module.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('admin.features.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    <!-- Icon Picker Modal -->
    <div id="iconModal" class="icon-modal hidden" onclick="closeIconPicker()">
        <div class="icon-modal-box" onclick="event.stopPropagation()">
            <h3>Choisir une icône</h3>
            <input id="iconSearch" type="text" placeholder="Rechercher une icône..." class="form-control">
            <div class="icon-picker-grid">
                @foreach ($icons as $icon)
                    <button type="button" class="icon-option" data-name="{{ $icon }}"
                        onclick="selectIcon(this,'{{ $icon }}')">
                        <x-dynamic-component :component="'heroicon-o-' . $icon" class="icon-svg" />
                        <span>{{ $icon }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ─── Icon Picker ───────────────────────────────────────────────
        function openIconPicker() {
            document.getElementById('iconModal').classList.remove('hidden');
        }

        function closeIconPicker() {
            document.getElementById('iconModal').classList.add('hidden');
        }

        function selectIcon(button, icon) {
            document.getElementById('selectedIcon').value = icon;
            document.getElementById('selectedIconName').innerText = icon;

            let svg = button.querySelector('svg').cloneNode(true);
            let preview = document.getElementById('selectedIconPreview');
            preview.innerHTML = '';
            preview.appendChild(svg);

            closeIconPicker();
        }

        document.getElementById('iconSearch').addEventListener('input', function () {
            let search = this.value.toLowerCase();
            document.querySelectorAll('.icon-option').forEach(button => {
                button.style.display = button.dataset.name.includes(search) ? 'flex' : 'none';
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeIconPicker();
        });

        // ─── Add Action (submit via hidden form) ───────────────────────
        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        function submitNewAction() {
            const nameVal = document.getElementById('new-action-name').value.trim();
            const codeVal = slugify(document.getElementById('new-action-code').value.trim());

            if (!nameVal || !codeVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs manquants',
                    text: 'Veuillez remplir le nom et le code de l\'action.',
                    confirmButtonColor: '#4f46e5',
                });
                return;
            }

            document.getElementById('hidden-action-name').value = nameVal;
            document.getElementById('hidden-action-code').value = codeVal;
            document.getElementById('add-action-form').submit();
        }

        // ─── Inline Edit Toggle ────────────────────────────────────────
        function showEditForm(id) {
            document.getElementById(`row-view-${id}`).style.display = 'none';
            document.getElementById(`row-edit-${id}`).style.display = 'table-row';
        }

        function hideEditForm(id) {
            document.getElementById(`row-view-${id}`).style.display = 'table-row';
            document.getElementById(`row-edit-${id}`).style.display = 'none';
        }

        // ─── Delete Action with SweetAlert2 ───────────────────────────
        function confirmDeleteAction(id, name) {
            Swal.fire({
                title: 'Supprimer cette action ?',
                html: `L'action <strong>${name}</strong> sera définitivement supprimée.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
@endsection
