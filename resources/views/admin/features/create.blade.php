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
            </div>
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
                    <input type="text" name="code" value="{{ old('code') }}" required
                        placeholder="Ex: users-management" class="form-control"
                        style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Route de Navigation (Nom de route Laravel)</label>
                    <input type="text" name="route_name" value="{{ old('route_name') }}"
                        placeholder="Ex: admin.users.index" class="form-control" style="font-family:monospace;">
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
                    <label class="form-label">Afficher dans le sidebar ?<span class="required-star">*</span></label>
                    <div style="position:relative;">
                        <select name="is_sidebar" required class="form-control">
                            <option value="1" {{ old('is_sidebar', '1') == '1' ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ old('is_sidebar') == '0' ? 'selected' : '' }}>Non</option>
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
                    <input type="number" name="order" value="{{ old('order', '0') }}" required min="0"
                        class="form-control">
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
            console.log("opening modal")
            document.getElementById('iconModal')
                .classList.remove('hidden');
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

        document.addEventListener('keydown', function(e) {

            if (e.key === "Escape") {
                closeIconPicker();
            }

        });
    </script>
@endsection
