@extends('layouts.admin')

@section('title', 'Ajouter un Module')

@section('page-title', 'Ajouter un Module')
@section('page-subtitle', 'Enregistrez un nouveau module et configurez ses preferences d\'affichage.')

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title"
            style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            Details du Module & Affichage
        </h3>

        @if ($errors->any())
            <div class="form-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>a
        @endif

        <form action="{{ route('admin.features.store') }}" method="POST">
            @csrf

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom du Module<span class="required-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ex: Gestion des Utilisateurs" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Code du Module (Unique)<span class="required-star">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Ex: users-management"
                        class="form-control"
                        style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Route de Navigation (Nom de route Laravel)</label>
                    <input type="text" name="route_name" value="{{ old('route_name') }}" placeholder="Ex: admin.users.index"
                        class="form-control" style="font-family:monospace;">
                </div>
                <div class="form-group">
                    <label class="form-label">Permission requise pour voir ce module (Action code)</label>
                    <input type="text" name="view_permission" value="{{ old('view_permission') }}"
                        placeholder="Ex: view-users" class="form-control" style="font-family:monospace;">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Statut du module<span class="required-star">*</span></label>
                    <div style="position:relative;">
                        <select name="is_sidebar" required class="form-control">
                            <option value="1" {{ old('is_sidebar', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_sidebar') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <span style="display:block;font-size:11px;color:#94a3b8;margin-top:5px;">
                        Active = le module est visible dans la barre de navigation. Inactive = masque complet.
                    </span>
                </div>
                <div class="form-group">
                    <label class="form-label">Ordre d'affichage dans la barre de navigation<span
                            class="required-star">*</span></label>
                    <input type="number" name="order" value="{{ old('order', '0') }}" required min="0" class="form-control">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">

                <label class="form-label">
                    Icône du module
                </label>


                <input type="hidden" name="icon" id="selectedIcon" value="{{ old('icon', 'users') }}">


                <button type="button" class="selected-icon-box" onclick="openIconPicker()">


                    <div id="selectedIconPreview">
                        <x-dynamic-component :component="'heroicon-o-' . old('icon', 'users')" class="icon-svg" />
                    </div>


                    <span id="selectedIconName">
                        {{ old('icon', 'users') }}
                    </span>


                    <span>
                        ▼
                    </span>


                </button>

            </div>
            <!-- Actions Section -->
            <div style="margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 24px;">
                <h3 class="data-title" style="margin-top: 0; margin-bottom: 20px; font-size: 15px;">
                    Actions Habilitées pour ce Nouveau Module
                </h3>

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
                        <button type="button" onclick="addClientAction()" class="btn-submit" style="padding: 10px 16px; font-size: 13px; white-space: nowrap;">
                            Ajouter
                        </button>
                    </div>
                </div>

                <div class="data-section" style="margin-bottom: 0;">
                    <table class="data-table" id="client-actions-table">
                        <thead>
                            <tr>
                                <th>Nom de l'Action</th>
                                <th>Code de l'Action</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="client-actions-list">
                            <tr id="empty-actions-row">
                                <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                                    Aucune action définie pour le moment.
                                </td>
                            </tr>
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

    <div id="iconModal" class="icon-modal hidden" onclick="closeIconPicker()">

        <div class="icon-modal-box" onclick="event.stopPropagation()">

            <h3>
                Choisir une icône
            </h3>


            <input id="iconSearch" type="text" placeholder="Rechercher une icône..." class="form-control">


            <div class="icon-picker-grid" id="iconPickerGrid">

                <div id="iconGridLoading" style="grid-column: 1 / -1; text-align: center; color: #94a3b8; font-style: italic;">
                    Chargement des icônes...
                </div>

            </div>


        </div>

    </div>

@endsection
@section('scripts')
    <script>
        let iconsLoaded = false;

        function loadIconGrid() {
            if (iconsLoaded) return;
            iconsLoaded = true;
            const grid = document.getElementById('iconPickerGrid');
            fetch('{{ route('admin.features.icon-grid') }}')
                .then(r => r.text())
                .then(html => { grid.innerHTML = html; })
                .catch(() => {
                    iconsLoaded = false;
                    document.getElementById('iconGridLoading').innerText =
                        'Erreur lors du chargement des icônes.';
                });
        }

        function openIconPicker() {
            console.log("opening modal")
            document.getElementById('iconModal')
                .classList.remove('hidden');
            loadIconGrid();
        }


        function closeIconPicker() {
            document.getElementById('iconModal')
                .classList.add('hidden');
        }


        function selectIcon(button, icon) {
            // save icon name in hidden input
            document.getElementById('selectedIcon').value = icon;


            // change text
            document.getElementById('selectedIconName')
                .innerText = icon;


            // copy clicked svg
            let svg = button.querySelector('svg').cloneNode(true);


            // replace preview
            let preview = document.getElementById('selectedIconPreview');

            preview.innerHTML = "";

            preview.appendChild(svg);


            // close popup
            closeIconPicker();
        }



        document.getElementById('iconSearch')
            .addEventListener('input', function () {

                let search = this.value.toLowerCase();


                document.querySelectorAll('.icon-option')
                    .forEach(button => {

                        let name = button.dataset.name;


                        if (name.includes(search)) {
                            button.style.display = "flex";
                        } else {
                            button.style.display = "none";
                        }

                    });

            });

        document.addEventListener('keydown', function (e) {

            if (e.key === "Escape") {
                closeIconPicker();
            }

        });

        // Client-side actions management
        let clientActions = [];
        let editIndex = -1;

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        function addClientAction() {
            const nameInput = document.getElementById('new-action-name');
            const codeInput = document.getElementById('new-action-code');
            const name = nameInput.value.trim();
            const code = slugify(codeInput.value.trim());

            if (!name || !code) {
                Swal.fire({ icon: 'warning', title: 'Attention', text: 'Veuillez remplir le nom et le code de l\'action.', confirmButtonColor: '#0066FF' });
                return;
            }

            // Check duplicate code
            if (clientActions.some(act => act.code === code)) {
                Swal.fire({ icon: 'warning', title: 'Attention', text: 'Ce code d\'action est déjà ajouté dans la liste.', confirmButtonColor: '#0066FF' });
                return;
            }

            clientActions.push({ name, code });
            nameInput.value = '';
            codeInput.value = '';
            renderClientActions();
        }

        function deleteClientAction(index) {
            clientActions.splice(index, 1);
            renderClientActions();
        }

        function editClientAction(index) {
            editIndex = index;
            renderClientActions();
        }

        function cancelEditClientAction() {
            editIndex = -1;
            renderClientActions();
        }

        function saveClientAction(index) {
            const nameInput = document.getElementById(`edit-act-name-${index}`);
            const codeInput = document.getElementById(`edit-act-code-${index}`);
            const name = nameInput.value.trim();
            const code = slugify(codeInput.value.trim());

            if (!name || !code) {
                Swal.fire({ icon: 'warning', title: 'Attention', text: 'Le nom et le code ne peuvent pas être vides.', confirmButtonColor: '#0066FF' });
                return;
            }

            // Check duplicate code in other items
            if (clientActions.some((act, idx) => act.code === code && idx !== index)) {
                Swal.fire({ icon: 'warning', title: 'Attention', text: 'Ce code d\'action est déjà utilisé dans une autre action.', confirmButtonColor: '#0066FF' });
                return;
            }

            clientActions[index] = { name, code };
            editIndex = -1;
            renderClientActions();
        }

        function renderClientActions() {
            const tbody = document.getElementById('client-actions-list');
            tbody.innerHTML = '';

            if (clientActions.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-actions-row">
                        <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                            Aucune action définie pour le moment.
                        </td>
                    </tr>
                `;
                return;
            }

            clientActions.forEach((action, index) => {
                if (editIndex === index) {
                    tbody.innerHTML += `
                        <tr>
                            <td colspan="3">
                                <div style="display: flex; gap: 12px; align-items: center; width: 100%; padding: 4px 0;">
                                    <div style="flex: 1;">
                                        <input type="text" id="edit-act-name-${index}" value="${action.name}" class="form-control" style="width:100%; padding: 6px 10px; font-size:13px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <input type="text" id="edit-act-code-${index}" value="${action.code}" class="form-control" style="width:100%; padding: 6px 10px; font-size:13px; font-family: monospace;">
                                    </div>
                                    <div style="display: flex; gap: 6px;">
                                        <button type="submit" style="display:none;"></button>
                                        <button type="button" onclick="saveClientAction(${index})" class="btn-submit" style="padding: 6px 12px; font-size: 12px; white-space: nowrap;">
                                            Enregistrer
                                        </button>
                                        <button type="button" onclick="cancelEditClientAction()" class="btn-cancel" style="padding: 6px 12px; font-size: 12px; white-space: nowrap;">
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML += `
                        <tr>
                            <td style="font-weight: 600; font-size: 13px; color: #1e293b;">
                                ${action.name}
                                <input type="hidden" name="actions[${index}][name]" value="${action.name}">
                            </td>
                            <td>
                                <span class="exam-code" style="color: #4f46e5; background: #e0e7ff;">${action.code}</span>
                                <input type="hidden" name="actions[${index}][code]" value="${action.code}">
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                    <button type="button" onclick="editClientAction(${index})" class="table-action-btn" title="Modifier" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748b;">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="deleteClientAction(${index})" class="table-action-btn archive-btn" title="Supprimer" style="background:none;border:none;cursor:pointer;padding:4px;color:#ef4444;">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            });
        }
    </script>
@endsection