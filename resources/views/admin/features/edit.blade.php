@extends('layouts.admin')

@section('title', 'Modifier le Module')

@section('page-title', 'Modifier le Module')
@section('page-subtitle', 'Mettez a jour la configuration du module et pilotez ses actions de securite.')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">

        <!-- Left Column: Feature Details Form -->
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
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code du Module (Unique)<span class="required-star">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $feature->code) }}" required
                            class="form-control"
                            style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                    </div>
                </div>

                <!-- Form Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Route de Navigation</label>
                        <input type="text" name="route_name" value="{{ old('route_name', $feature->route_name) }}"
                            placeholder="Ex: admin.users.index" class="form-control" style="font-family:monospace;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Permission de visibilite (Action code)</label>
                        <input type="text" name="view_permission"
                            value="{{ old('view_permission', $feature->view_permission) }}" placeholder="Ex: view-users"
                            class="form-control" style="font-family:monospace;">
                    </div>
                </div>

                <!-- Form Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Afficher dans le sidebar ?<span class="required-star">*</span></label>
                        <div style="position:relative;">
                            <select name="is_sidebar" required class="form-control">
                                <option value="1" {{ old('is_sidebar', $feature->is_sidebar) ? 'selected' : '' }}>Oui
                                </option>
                                <option value="0" {{ !old('is_sidebar', $feature->is_sidebar) ? 'selected' : '' }}>Non
                                </option>
                            </select>
                            <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ordre d'affichage dans la barre de nav<span
                                class="required-star">*</span></label>
                        <input type="number" name="order" value="{{ old('order', $feature->order) }}" required
                            min="0" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">

                    <label class="form-label">
                        Icône du module
                    </label>


                    <input type="hidden" name="icon" id="selectedIcon"
                        value="{{ old('icon', $feature->icon ?? 'users') }}">


                    <button type="button" class="selected-icon-box" onclick="openIconPicker()">


                        <x-dynamic-component :component="'heroicon-o-' . old('icon', $feature->icon ?? 'users')" id="selectedIconPreview" class="icon-svg" />


                        <span id="selectedIconName">
                            {{ old('icon', $feature->icon ?? 'users') }}
                        </span>


                        <span>
                            ▼
                        </span>

                    </button>

                </div>

                <!-- Footer actions -->
                <div
                    style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                    <a href="{{ route('admin.features.index') }}" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-submit">Enregistrer les modifications</button>
                </div>
            </form>
        </div>

        <!-- Right Column: Actions Management -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- Sub-section: Add Action -->
            <div class="data-section anim anim-2" style="padding: 24px;">
                <h3 class="data-title"
                    style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 15px;">
                    Ajouter une nouvelle Action de Securite
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

                <form action="{{ route('admin.features.actions.store', $feature) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nom de l'Action<span class="required-star">*</span></label>
                            <input type="text" name="action_name" required value="{{ old('action_name') }}"
                                placeholder="Ex: Creer utilisateurs" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Code de l'Action (Unique)<span
                                    class="required-star">*</span></label>
                            <input type="text" name="action_code" required value="{{ old('action_code') }}"
                                placeholder="Ex: create-users" class="form-control"
                                style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
                        <button type="submit" class="btn-submit" style="padding: 8px 16px; font-size: 13px;">
                            Ajouter l'Action
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sub-section: Actions List -->
            <div class="data-section anim anim-3">
                <div class="data-header" style="padding: 16px 20px;">
                    <div class="data-title" style="font-size: 14px;">Actions Habilitees pour ce Module</div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Code unique</th>
                            <th>Statut</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actions as $action)
                            <tr class="{{ $action->is_archive ? 'archived' : '' }}">
                                <td style="font-weight: 600; font-size: 13px; color: #1e293b;">
                                    {{ $action->name }}
                                </td>
                                <td>
                                    <span class="exam-code"
                                        style="color: #4f46e5; background: #e0e7ff;">{{ $action->code }}</span>
                                </td>
                                <td>
                                    @if ($action->is_archive)
                                        <span class="status-badge status-archived"><span
                                                class="dot"></span>Archive</span>
                                    @else
                                        <span class="status-badge status-active"><span class="dot"></span>Actif</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.actions.destroy', $action) }}" method="POST"
                                        style="display:inline;margin:0;"
                                        onsubmit="return confirm('{{ $action->is_archive ? 'Restaurer cette action ?' : 'Archiver cette action ?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="table-action-btn {{ $action->is_archive ? 'restore-btn' : 'archive-btn' }}"
                                            title="{{ $action->is_archive ? 'Restaurer' : 'Archiver' }}">
                                            @if ($action->is_archive)
                                                <svg fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                </svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                                    Aucune action definie pour ce module.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div id="iconModal" class="icon-modal hidden" onclick="closeIconPicker()">

        <div class="icon-modal-box" onclick="event.stopPropagation()">

            <h3>
                Choisir une icône
            </h3>


            <input id="iconSearch" type="text" placeholder="Rechercher une icône..." class="form-control">


            <div class="icon-picker-grid">


                @foreach ($icons as $icon)
                    <button type="button" class="icon-option" data-name="{{ $icon }}"
                        onclick="selectIcon(this,'{{ $icon }}')">


                        <x-dynamic-component :component="'heroicon-o-' . $icon" class="icon-svg" />


                        <span>
                            {{ $icon }}
                        </span>


                    </button>
                @endforeach


            </div>


        </div>

    </div>
@endsection
@section('scripts')

    <script>
        function openIconPicker() {
            document.getElementById('iconModal')
                .classList.remove('hidden');
        }


        function closeIconPicker() {
            document.getElementById('iconModal')
                .classList.add('hidden');
        }


        function selectIcon(button, icon) {
            document.getElementById('selectedIcon').value = icon;


            document.getElementById('selectedIconName')
                .innerText = icon;


            let svg = button.querySelector('svg').cloneNode(true);


            let preview = document.getElementById('selectedIconPreview');


            preview.innerHTML = "";


            preview.appendChild(svg);


            closeIconPicker();
        }



        document.getElementById('iconSearch')
            .addEventListener('input', function() {

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
    </script>

@endsection
