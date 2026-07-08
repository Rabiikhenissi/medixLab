@extends('layouts.admin')

@section('title', 'Créer un Rôle')

@section('page-title', 'Nouveau Rôle de Sécurité')
@section('page-subtitle', 'Définissez le nom, le code du rôle et configurez ses habilitations de sécurité.')

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title" style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            Détails du Rôle & Matrice des Habilitations
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

        <form action="{{ route('admin.groups.store') }}" method="POST">
            @csrf

            <!-- Form Row -->
            <div class="form-row" style="margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">Nom du Rôle<span class="required-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Directeur de Laboratoire" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Code de sécurité (Unique)<span class="required-star">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Ex: directeur-labo" class="form-control" style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                </div>
            </div>

            <!-- Permission grid header -->
            <h4 class="form-label" style="font-size: 12px; margin-bottom: 8px;">Matrice des Habilitations par Fonctionnalité</h4>

            <!-- Permission Grid -->
            <div class="permissions-grid">
                @foreach($features as $feature)
                    <div class="feature-card">
                        <div class="feature-name">{{ $feature->name }}</div>
                        @forelse($feature->actions as $action)
                            <label class="action-checkbox">
                                <input type="checkbox" name="actions[]" value="{{ $action->id }}"
                                    {{ is_array(old('actions')) && in_array($action->id, old('actions')) ? 'checked' : '' }}>
                                {{ $action->name }}
                            </label>
                        @empty
                            <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Aucune action enregistrée</span>
                        @endforelse
                    </div>
                @endforeach
            </div>

            <!-- Footer actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                <a href="{{ route('admin.groups.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Créer le rôle</button>
            </div>
        </form>
    </div>
@endsection
