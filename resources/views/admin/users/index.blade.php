@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('page-title', 'Utilisateurs')
@section('page-subtitle', 'Gérez les comptes des médecins, des patients, des administrateurs et du personnel du centre.')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn-add-exam">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Créer un Utilisateur
    </a>
@endsection

@section('content')
    <div class="data-section anim anim-1">
        <!-- Table Header -->
        <div class="data-header">
            <div class="data-title">Liste des Comptes Utilisateurs</div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form">
            <div class="filters-bar">
                <!-- Group/Role Filter -->
                <div>
                    <span class="filter-label">Rôle / Groupe</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="group_id" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Tous les rôles</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <!-- Search -->
                <div>
                    <span class="filter-label">Recherche rapide</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom, email, téléphone..." class="filter-input">
                    </div>
                </div>

                <!-- Archived toggle -->
                <div style="align-self:flex-end;">
                    <label class="filter-checkbox-wrap">
                        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()">
                        Afficher archivés
                    </label>
                </div>

                <!-- Filter Button -->
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">Filtrer</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle / Groupe</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="{{ $user->is_archive ? 'archived' : '' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="nav-avatar" style="width:30px;height:30px;font-size:11px;">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="exam-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    @if($user->address)
                                        <div class="exam-desc">{{ $user->address }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: #475569;">{{ $user->email }}</div>
                        </td>
                        <td>
                            <div style="color: #64748b;">{{ $user->phone ?? '—' }}</div>
                        </td>
                        <td>
                            @if($user->group)
                                <span class="category-badge cat-{{ $user->group->code === 'admin' ? 'biochemistry' : ($user->group->code === 'doctor' ? 'microbiology' : ($user->group->code === 'patient' ? 'hematology' : 'other')) }}">
                                    {{ $user->group->name }}
                                </span>
                            @else
                                <span class="category-badge cat-other">Aucun</span>
                            @endif
                        </td>
                        <td style="color:#94a3b8;font-size:12px;white-space:nowrap;">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}
                        </td>
                        <td>
                            @if($user->is_archive)
                                <span class="status-badge status-archived"><span class="dot"></span>Archivé</span>
                            @else
                                <span class="status-badge status-active"><span class="dot"></span>Actif</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <!-- Edit Link -->
                                <a href="{{ route('admin.users.edit', $user) }}" class="table-action-btn" title="Modifier">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </a>

                                <!-- Archive/Restore Form -->
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;margin:0;"
                                          onsubmit="return confirm('{{ $user->is_archive ? 'Restaurer cet utilisateur ?' : 'Archiver cet utilisateur ?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-btn {{ $user->is_archive ? 'restore-btn' : 'archive-btn' }}" title="{{ $user->is_archive ? 'Restaurer' : 'Archiver' }}">
                                            @if($user->is_archive)
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <button class="table-action-btn" style="opacity: 0.3; cursor: not-allowed;" title="Vous ne pouvez pas archiver votre propre compte.">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3>Aucun utilisateur trouvé</h3>
                                <p>Créez un nouveau compte utilisateur pour commencer.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if($users->onFirstPage())
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">« Précédent</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">« Précédent</a>
                    @endif

                    @foreach($users->getUrlRange(max(1, $users->currentPage()-2), min($users->lastPage(), $users->currentPage()+2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <span style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">Suivant »</a>
                    @else
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">Suivant »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
