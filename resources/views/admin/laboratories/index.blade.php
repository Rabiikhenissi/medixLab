@extends('layouts.admin')

@section('title', 'Gestion des Laboratoires')

@section('page-title', 'Laboratoires')
@section('page-subtitle', 'Gérez les établissements médicaux (laboratoires) enregistrés sur la plateforme.')

@section('header-actions')
    <a href="{{ route('admin.laboratories.create') }}" class="btn-add-exam">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajouter un Laboratoire
    </a>
@endsection

@section('content')
    <div class="data-section anim anim-1">
        <!-- Table Header -->
        <div class="data-header">
            <div class="data-title">Liste des Laboratoires</div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.laboratories.index') }}" id="filter-form">
            <div class="filters-bar">
                <!-- Search -->
                <div>
                    <span class="filter-label">Recherche rapide</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Rechercher par nom, ville, email..." class="filter-input">
                    </div>
                </div>

                <!-- Archived toggle -->
                <div style="align-self:flex-end;">
                    <label class="filter-checkbox-wrap">
                        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }}
                            onchange="document.getElementById('filter-form').submit()">
                        Afficher archivés
                    </label>
                </div>

                <!-- Filter Button -->
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">Rechercher</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom du Laboratoire</th>
                    <th>Ville</th>
                    <th>Email / Téléphone</th>
                    <th>Adresse</th>
                    <th>Statut</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laboratories as $labo)
                    <tr class="{{ $labo->is_archive ? 'archived' : '' }}">
                        <td>
                            <div class="exam-name">{{ $labo->name }}</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">ID: #{{ $labo->id }}</div>
                        </td>
                        <td>
                            <span class="category-badge cat-biochemistry" style="font-weight: 700;">{{ $labo->city ?? 'Non renseignée' }}</span>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $labo->email ?? '-' }}</div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $labo->phone ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="exam-desc" style="max-width:300px;">{{ $labo->address ?? '-' }}</div>
                        </td>
                        <td>
                            @if ($labo->is_archive)
                                <span class="status-badge status-archived"><span class="dot"></span>Archivé</span>
                            @else
                                <span class="status-badge status-active"><span class="dot"></span>Actif</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <!-- Edit Link -->
                                <a href="{{ route('admin.laboratories.edit', $labo) }}" class="table-action-btn"
                                    title="Modifier">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>

                                <!-- Archive/Restore Form -->
                                <form action="{{ route('admin.laboratories.destroy', $labo) }}" method="POST"
                                    style="display:inline;margin:0;"
                                    onsubmit="return swalConfirmSubmit(this, '{{ $labo->is_archive ? 'Restaurer ce laboratoire ?' : 'Archiver ce laboratoire ?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="table-action-btn {{ $labo->is_archive ? 'restore-btn' : 'archive-btn' }}"
                                        title="{{ $labo->is_archive ? 'Restaurer' : 'Archiver' }}">
                                        @if ($labo->is_archive)
                                            <svg fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                @if($labo->is_archive)
                                    <form action="{{ route('admin.laboratories.force-delete', $labo) }}" method="POST" style="display:inline;margin:0;"
                                          onsubmit="return swalConfirmSubmit(this, 'Supprimer définitivement ce laboratoire ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-btn delete-btn" title="Supprimer définitivement">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.75 3.104v13.01m0-13.01L6 6.854m3.75-3.75l3.75 3.75M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                </div>
                                <h3>Aucun laboratoire trouvé</h3>
                                <p>Créez un nouveau laboratoire pour démarrer.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($laboratories->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if ($laboratories->onFirstPage())
                        <span
                            style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">«
                            Précédent</span>
                    @else
                        <a href="{{ $laboratories->previousPageUrl() }}"
                            style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">«
                            Précédent</a>
                    @endif

                    @foreach ($laboratories->getUrlRange(max(1, $laboratories->currentPage() - 2), min($laboratories->lastPage(), $laboratories->currentPage() + 2)) as $page => $url)
                        @if ($page == $laboratories->currentPage())
                            <span
                                style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($laboratories->hasMorePages())
                        <a href="{{ $laboratories->nextPageUrl() }}"
                            style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">Suivant
                            »</a>
                    @else
                        <span
                            style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">Suivant
                            »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
