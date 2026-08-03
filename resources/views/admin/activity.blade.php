@extends('layouts.admin')

@section('title', 'Journal d\'Activité')

@section('page-title', 'Journal d\'Activité')
@section('page-subtitle', 'Traçabilité médico-légale : toutes les actions sensibles, horodatées et immuables.')

@section('content')
    <div class="data-section anim anim-1">
        <div class="data-header">
            <div class="data-title">Journal des actions</div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.activity') }}" id="filter-form">
            <div class="filters-bar">
                <div>
                    <span class="filter-label">Entité</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="entity" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Toutes les entités</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <div>
                    <span class="filter-label">Action</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="action" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Toutes les actions</option>
                            @foreach(['created' => 'Création', 'updated' => 'Modification', 'deleted' => 'Suppression', 'restored' => 'Restauration'] as $code => $label)
                                <option value="{{ $code }}" {{ request('action') == $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <div>
                    <span class="filter-label">Recherche rapide</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une description, IP..." class="filter-input">
                    </div>
                </div>

                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">Filtrer</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Entité</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;color:#64748b;font-size:12px;">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            @if($log->user)
                                <div style="font-weight:500;color:#1e293b;font-size:13px;">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $log->role ? ucfirst($log->role) : '—' }}</div>
                            @else
                                <span class="category-badge cat-other">Système</span>
                            @endif
                        </td>
                        <td>
                            @if($log->action === 'created')
                                <span class="status-badge status-active" style="background:#eff6ff;color:#2563eb;"><span class="dot" style="background:#2563eb;"></span>Création</span>
                            @elseif($log->action === 'updated')
                                <span class="status-badge" style="background:#fffbeb;color:#b45309;"><span class="dot" style="background:#f59e0b;"></span>Modification</span>
                            @elseif($log->action === 'deleted')
                                <span class="status-badge" style="background:#fff1f2;color:#e11d48;"><span class="dot" style="background:#ef4444;"></span>Suppression</span>
                            @else
                                <span class="status-badge status-archived"><span class="dot"></span>{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="exam-code">{{ $log->entity_type }}</span>
                            @if($log->entity_id)
                                <span style="color:#94a3b8;font-size:12px;">#{{ $log->entity_id }}</span>
                            @endif
                        </td>
                        <td style="max-width:340px;">
                            <div style="font-size:13px;color:#374151;">{{ $log->description }}</div>
                            @if($log->changes)
                                <details style="margin-top:4px;">
                                    <summary style="font-size:11px;color:#0066ff;cursor:pointer;">Détails des changements</summary>
                                    <pre style="font-size:11px;background:#f8fafc;border:1px solid #e8eef4;border-radius:8px;padding:10px;margin:6px 0 0;overflow:auto;max-height:180px;color:#475569;font-family:'SF Mono','Consolas',monospace;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#94a3b8;font-family:'SF Mono','Consolas',monospace;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3>Aucune activité enregistrée</h3>
                                <p>Les actions sensibles apparaîtront ici automatiquement.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if($logs->onFirstPage())
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">« Précédent</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">« Précédent</a>
                    @endif

                    @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                        @if($page == $logs->currentPage())
                            <span style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">Suivant »</a>
                    @else
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">Suivant »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
